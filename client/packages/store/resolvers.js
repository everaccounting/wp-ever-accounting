import { DEFAULT_PRIMARY_KEY, STORE_NAME } from './constants';
import { forwardResolver, getNormalizedCommaSeparable } from './utils';
import { addQueryArgs } from '@wordpress/url';
import apiFetch from '@wordpress/api-fetch';
import { identity, pickBy } from 'lodash';

/**
 * Requests an entity's record from the REST API.
 *
 * @param {string}           name  Entity name.
 * @param {number|string}    key   Record's key
 * @param {Object|undefined} query Optional object of query parameters to
 *                                 include with request. If requesting specific
 *                                 fields, fields must always include the ID.
 */
export const getEntityRecord =
	(name, key = '', query) =>
	async ({ select, dispatch }) => {
		const entityConfig = await select.getEntity(name);
		if (!entityConfig) {
			return;
		}

		try {
			if (query !== undefined && query._fields) {
				// If requesting specific fields, items and query association to said
				// records are stored by ID reference. Thus, fields must always include
				// the ID.
				query = {
					...query,
					_fields: [
						...new Set([
							...(getNormalizedCommaSeparable(query._fields) ||
								[]),
							entityConfig.key || DEFAULT_PRIMARY_KEY,
						]),
					].join(),
				};
			}

			// Disable reason: While true that an early return could leave `path`
			// unused, it's important that path is derived using the query prior to
			// additional query modifications in the condition below, since those
			// modifications are relevant to how the data is tracked in state, and not
			// for how the request is made to the REST API.

			// eslint-disable-next-line @wordpress/no-unused-vars-before-return
			const path = addQueryArgs(
				entityConfig.baseURL + (key ? '/' + key : ''),
				{
					...entityConfig.baseURLParams,
					...query,
				}
			);

			if (query !== undefined) {
				query = { ...query, include: [key] };

				// The resolution cache won't consider query as reusable based on the
				// fields, so it's tested here, prior to initiating the REST request,
				// and without causing `getEntityRecords` resolution to occur.
				const hasRecords = select.hasEntityRecords(name, query);
				if (hasRecords) {
					return;
				}
			}

			const record = await apiFetch({ path });
			dispatch.receiveEntityRecords(name, record, query);
		} catch (error) {
			// If the record was not found, invalidate the cache.
			console.error(error);
		}
	};

/**
 * Requests an entity's record from the REST API.
 */
export const getRawEntityRecord = forwardResolver('getEntityRecord');
/**
 * Requests an entity's record from the REST API.
 */
export const getEditedEntityRecord = forwardResolver('getEntityRecord');

/**
 * Requests the entity's records from the REST API.
 *
 * @param {string}  name  Entity name.
 * @param {Object?} query Query Object. If requesting specific fields, fields
 *                        must always include the ID.
 */
export const getEntityRecords =
	(name, query = {}) =>
	async ({ select, dispatch }) => {
		const entityConfig = await select.getEntity(name);
		if (!entityConfig) {
			return;
		}

		try {
			if (query._fields) {
				// If requesting specific fields, items and query association to said
				// records are stored by ID reference. Thus, fields must always include
				// the ID.
				query = {
					...query,
					_fields: [
						...new Set([
							...(getNormalizedCommaSeparable(query._fields) ||
								[]),
							entityConfig.key || DEFAULT_PRIMARY_KEY,
						]),
					].join(),
				};
			}

			const path = addQueryArgs(entityConfig.baseURL, {
				...entityConfig.baseURLParams,
				...query,
			});

			const response = await apiFetch({ path, parse: false });
			let records = await response.json();
			const total = parseInt(response.headers.get('x-wp-total'), 10);
			// If we request fields but the result doesn't contain the fields,
			// explicitly set these fields as "undefined"
			// that way we consider the query "fullfilled".
			if (query._fields) {
				records = records.map((record) => {
					query._fields.split(',').forEach((field) => {
						if (!record.hasOwnProperty(field)) {
							record[field] = undefined;
						}
					});

					return record;
				});
			}

			// dispatch the events from the store eac/store
			dispatch.receiveEntityRecords(name, records, query);
			dispatch.receiveEntityRecordsTotal(name, total, query);

			// When requesting all fields, the list of results can be used to
			// resolve the `getEntityRecord` selector in addition to `getEntityRecords`.
			// See https://github.com/WordPress/gutenberg/pull/26575
			if (!query?._fields && !query.context) {
				const key = entityConfig.key || DEFAULT_PRIMARY_KEY;
				const resolutionsArgs = records
					.filter((record) => record[key])
					.map((record) => [name, record[key]]);

				dispatch({
					type: 'START_RESOLUTIONS',
					selectorName: 'getEntityRecord',
					args: resolutionsArgs,
				});
				dispatch({
					type: 'FINISH_RESOLUTIONS',
					selectorName: 'getEntityRecord',
					args: resolutionsArgs,
				});
			}
		} catch (error) {
			console.error(error);
		}
	};

getEntityRecords.shouldInvalidate = (action, name) => {
	return (
		(action.type === 'RECEIVE_ITEMS' || action.type === 'REMOVE_ITEMS') &&
		action.invalidateCache &&
		name === action.name
	);
};

/**
 * Get entity count.
 *
 * @param {string} name  Entity name.
 * @param {Object} query Query object.
 *
 * @return {number} Entity count.
 */
export const getEntityRecordsTotal =
	(name, query) =>
	async ({ select, resolveSelect }) => {
		const entityConfig = await select.getEntity(name);
		if (!entityConfig) {
			return;
		}

		try {
			await resolveSelect.getEntityRecords(name, query);
			return select.getEntityRecordsTotal(name, query);
		} catch (error) {
			console.log(error);
		}
	};

/**
 * Requests the current user from the REST API.
 */
export const getCurrentUser =
	() =>
	async ({ dispatch }) => {
		const currentUser = await apiFetch({ path: '/wp/v2/users/me' });
		dispatch.receiveCurrentUser(currentUser);
	};

/**
 * Checks whether the current user can perform the given action on the given
 * REST resource.
 *
 * @param {string}  requestedAction Action to check. One of: 'create', 'read', 'update',
 *                                  'delete'.
 * @param {string}  resource        REST resource to check, e.g. 'media' or 'posts'.
 * @param {?string} id              ID of the rest resource to check.
 */
export const canUser =
	(requestedAction, resource, id) =>
	async ({ dispatch, registry }) => {
		const { hasStartedResolution } = registry.select(STORE_NAME);

		const resourcePath = id ? `${resource}/${id}` : resource;
		const retrievedActions = ['create', 'read', 'update', 'delete'];

		if (!retrievedActions.includes(requestedAction)) {
			throw new Error(`'${requestedAction}' is not a valid action.`);
		}

		// Prevent resolving the same resource twice.
		for (const relatedAction of retrievedActions) {
			if (relatedAction === requestedAction) {
				continue;
			}
			const isAlreadyResolving = hasStartedResolution('canUser', [
				relatedAction,
				resource,
				id,
			]);
			if (isAlreadyResolving) {
				return;
			}
		}

		let response;
		try {
			response = await apiFetch({
				path: `/wp/v2/${resourcePath}`,
				method: 'OPTIONS',
				parse: false,
			});
		} catch (error) {
			// Do nothing if our OPTIONS request comes back with an API error (4xx or
			// 5xx). The previously determined isAllowed value will remain in the store.
			return;
		}

		// Optional chaining operator is used here because the API requests don't
		// return the expected result in the native version. Instead, API requests
		// only return the result, without including response properties like the headers.
		const allowHeader = response.headers?.get('allow');
		const allowedMethods = allowHeader?.allow || allowHeader || '';

		const permissions = {};
		const methods = {
			create: 'POST',
			read: 'GET',
			update: 'PUT',
			delete: 'DELETE',
		};
		for (const [actionName, methodName] of Object.entries(methods)) {
			permissions[actionName] = allowedMethods.includes(methodName);
		}

		for (const action of retrievedActions) {
			dispatch.receiveUserPermission(
				`${action}/${resourcePath}`,
				permissions[action]
			);
		}
	};

/**
 * Checks whether the current user can perform the given action on the given
 * REST resource.
 *
 * @param {string} name     Entity name.
 * @param {string} recordId Record's id.
 */
export const canUserEditEntityRecord =
	(name, recordId) =>
	async ({ dispatch, select }) => {
		const entityConfig = await select.getEntity(name);
		if (!entityConfig) {
			return;
		}

		const resource = entityConfig.__unstable_rest_base;
		await dispatch(canUser('update', resource, recordId));
	};
