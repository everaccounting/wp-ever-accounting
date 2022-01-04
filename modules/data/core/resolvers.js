/**
 * Internal dependencies
 */
import {
	apiFetch,
	apiFetchWithHeaders,
	resolveSelect,
	select,
} from '../base-controls';
import { STORE_NAME } from './constants';
/**
 * External dependencies
 */
import { compact, get, hasIn, identity, includes, pickBy, uniq } from 'lodash';
import {
	receiveEntityError,
	receiveEntityRecords,
	receiveTotalEntityRecords,
	receiveSettings,
	receiveCurrentUser,
	receiveUserPermission,
} from './actions';
import { getNormalizedCommaSeparable } from '../utils';
/**
 * WordPress dependencies
 */
import { addQueryArgs } from '@wordpress/url';

/**
 * Requests an entity's record from the REST API.
 *
 * @param {string}           name  Entity name.
 * @param {number|string}    key   Record's key
 * @param {Object|undefined} query Optional object of query parameters to
 *                                 include with request.
 */
export function* getEntityRecord(name, key, query = {}) {
	console.log('getEntityRecord Resolver', key);

	if (!key) {
		return {};
	}
	const entity = yield select(STORE_NAME, 'getEntity', name);
	if (!entity) {
		throw `Could not find any entity named "${name}" please check entity config`;
	}
	const { route, primaryKey } = entity;
	if (query && query.hasOwnProperty('_fields')) {
		// If requesting specific fields, items and query association to said
		// records are stored by ID reference. Thus, fields must always include
		// the ID.
		query = {
			...query,
			_fields: uniq([
				...(getNormalizedCommaSeparable(query._fields) || []),
				primaryKey,
			]).join(),
		};
	}

	if (query && query.hasOwnProperty('_fields')) {
		query = { ...query, include: [key] };

		// The resolution cache won't consider query as reusable based on the
		// fields, so it's tested here, prior to initiating the REST request,
		// and without causing `getEntities` resolution to occur.
		const hasRecords = yield select(
			STORE_NAME,
			'getEntityRecords',
			name,
			query
		);
		if (hasRecords) {
			return;
		}
	}
	const path = addQueryArgs(route + '/' + key, {
		...pickBy(query, identity),
	});
	try {
		const item = yield apiFetch({ path });
		yield receiveEntityRecords(name, item, query, primaryKey);
		yield receiveEntityError(
			name,
			{},
			{
				...query,
				key,
			}
		);
	} catch (error) {
		yield receiveEntityError(name, error, {
			...query,
			key,
		});
	}
}

/**
 * Requests the entity's records from the REST API.
 *
 * @param {string}  name   Entity name.
 * @param {Object?} query  Query Object.
 */
export function* getEntityRecords(name, query = {}) {
	const entity = yield select(STORE_NAME, 'getEntity', name);

	if (!entity) {
		throw `Could not find any entity named "${name}" please check entity config`;
	}
	const { route, primaryKey } = entity;
	if (query && query.hasOwnProperty('_fields')) {
		// If requesting specific fields, items and query association to said
		// records are stored by ID reference. Thus, fields must always include
		// the ID.
		query = {
			...query,
			_fields: uniq([
				...(getNormalizedCommaSeparable(query._fields) || []),
				primaryKey,
			]).join(),
		};
	}

	const path = addQueryArgs(route, {
		...pickBy(query, identity),
		context: 'edit',
	});

	try {
		const { data, headers } = yield apiFetchWithHeaders({ path });
		const total = parseInt(headers.get('x-wp-total'), 10);
		yield receiveEntityRecords(name, data, query, primaryKey);
		yield receiveTotalEntityRecords(name, total, query);
		yield receiveEntityError(name, {}, query);
		// When requesting all fields, the list of results can be used to
		// resolve the `getEntityRecords` selector in addition to `getEntityRecords`.
		// See https://github.com/WordPress/gutenberg/pull/26575
		if (!query?._fields) {
			for (const item of data) {
				if (item[primaryKey]) {
					yield {
						type: 'START_RESOLUTION',
						selectorName: 'getEntityRecord',
						args: [name, item[primaryKey]],
					};
					yield {
						type: 'FINISH_RESOLUTION',
						selectorName: 'getEntityRecord',
						args: [name, item[primaryKey]],
					};
				}
			}
		}
	} catch (error) {
		yield receiveEntityError(name, error, query);
	}
}

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
 * @param {string} name
 * @param {Object} query
 */
export function* getTotalEntityRecords(name, query = {}) {
	yield resolveSelect(STORE_NAME, 'getEntityRecords', name, query);
}

/**
 * Retrieves options value from the options store.
 */
export function* getOptions() {
	try {
		const settings = yield apiFetch({ path: '/ea/v1/settings' });
		yield receiveSettings(settings);
	} catch (error) {
		receiveSettings({}, error);
	}
}

/**
 * Retrieves an option value from the options store.
 *
 **/
export function* getOption() {
	yield resolveSelect(STORE_NAME, 'getOptions');
}

/**
 * Requests the current user from the REST API.
 */
export function* getCurrentUser() {
	const currentUser = yield apiFetch({ path: '/wp/v2/users/me' });
	yield receiveCurrentUser(currentUser);
}

/**
 * Checks whether the current user can perform the given action on the given
 * REST resource.
 *
 * @param {string}  action   Action to check. One of: 'create', 'read', 'update',
 *                           'delete'.
 * @param {string}  resource REST resource to check, e.g. 'media' or 'posts'.
 * @param {?string} id       ID of the rest resource to check.
 */
export function* canUser(action, resource, id) {
	const methods = {
		create: 'POST',
		read: 'GET',
		update: 'PUT',
		delete: 'DELETE',
	};

	const method = methods[action];
	if (!method) {
		throw new Error(`'${action}' is not a valid action.`);
	}

	const path = id ? `/wp/v2/${resource}/${id}` : `/wp/v2/${resource}`;

	let response;
	try {
		response = yield apiFetch({
			path,
			// Ideally this would always be an OPTIONS request, but unfortunately there's
			// a bug in the REST API which causes the Allow header to not be sent on
			// OPTIONS requests to /posts/:id routes.
			// https://core.trac.wordpress.org/ticket/45753
			method: id ? 'GET' : 'OPTIONS',
			parse: false,
		});
	} catch (error) {
		// Do nothing if our OPTIONS request comes back with an API error (4xx or
		// 5xx). The previously determined isAllowed value will remain in the store.
		return;
	}

	let allowHeader;
	if (hasIn(response, ['headers', 'get'])) {
		// If the request is fetched using the fetch api, the header can be
		// retrieved using the 'get' method.
		allowHeader = response.headers.get('allow');
	} else {
		// If the request was preloaded server-side and is returned by the
		// preloading middleware, the header will be a simple property.
		allowHeader = get(response, ['headers', 'Allow'], '');
	}

	const key = compact([action, resource, id]).join('/');
	const isAllowed = includes(allowHeader, method);
	yield receiveUserPermission(key, isAllowed);
}
