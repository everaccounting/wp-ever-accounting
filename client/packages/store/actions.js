import { DEFAULT_PRIMARY_KEY } from './constants';
import apiFetch from '@wordpress/api-fetch';
import fastDeepEqual from 'fast-deep-equal/es6';
import { addQueryArgs } from '@wordpress/url';
import {
	receiveItems,
	receiveQueriedItems,
	receiveItemTotal,
	receiveError,
	removeItems,
} from './queried-data';

/**
 * Returns an action used in signalling that the current user has been received.
 * Ignored from documentation as it's internal to the data store.
 *
 * @ignore
 *
 * @param {Object} currentUser Current user object.
 *
 * @return {Object} Action object.
 */
export function receiveCurrentUser(currentUser) {
	return {
		type: 'RECEIVE_CURRENT_USER',
		currentUser,
	};
}

/**
 * Returns an action object used in adding new entities.
 *
 * @param {Array} entities Entities received.
 *
 * @return {Object} Action object.
 */
export function addEntities(entities) {
	return {
		type: 'ADD_ENTITIES',
		entities,
	};
}

/**
 * Returns an action object used in signalling that entity records have been received.
 *
 * @param {string}       name            Name of the received entity.
 * @param {Array|Object} records         Records received.
 * @param {?Object}      query           Query Object.
 * @param {?boolean}     invalidateCache Should invalidate query caches.
 * @param {?Object}      edits           Edits to reset.
 * @return {Object} Action object.
 */
export function receiveEntityRecords(
	name,
	records,
	query,
	invalidateCache = false,
	edits
) {
	let action;
	if (query) {
		action = receiveQueriedItems(records, query, edits);
	} else {
		action = receiveItems(records, edits);
	}

	return {
		...action,
		name,
		invalidateCache,
	};
}

/**
 *
 * @param { string } name            Entity Name
 * @param { number } total           count
 * @param {Object}   query
 * @param {boolean}  invalidateCache
 * @return {{total: number, invalidateCache: boolean, query: ?Object, name, type: string}} Receive Total.
 */
export function receiveEntityRecordsTotal(
	name,
	total,
	query,
	invalidateCache = false
) {
	return {
		...receiveItemTotal(total, query),
		name,
		invalidateCache,
	};
}

/**
 * Action triggered to delete an entity record.
 *
 * @param {string}   name                         Name of the deleted entity.
 * @param {string}   recordId                     Record ID of the deleted entity.
 * @param {?Object}  query                        Special query parameters for the
 *                                                DELETE API call.
 * @param {Object}   [options]                    Delete options.
 * @param {Function} [options.__unstableFetch]    Internal use only. Function to
 *                                                call instead of `apiFetch()`.
 *                                                Must return a promise.
 * @param {boolean}  [options.throwOnError=false] If false, this action suppresses all
 *                                                the exceptions. Defaults to false.
 */
export const deleteEntityRecord =
	(
		name,
		recordId,
		query,
		{ __unstableFetch = apiFetch, throwOnError = false } = {}
	) =>
	async ({ dispatch, select }) => {
		const entityConfig = await select.getEntity(name);
		let error;
		let deletedRecord = false;
		if (!entityConfig) {
			return;
		}
		try {
			dispatch({
				type: 'DELETE_ENTITY_RECORD_START',
				name,
				recordId,
			});

			let hasError = false;
			try {
				let path = `${entityConfig.baseURL}/${recordId}`;

				if (query) {
					path = addQueryArgs(path, query);
				}

				deletedRecord = await __unstableFetch({
					path,
					method: 'DELETE',
				});

				await dispatch(removeItems(name, recordId, true));
			} catch (_error) {
				hasError = true;
				error = _error;
			}

			dispatch({
				type: 'DELETE_ENTITY_RECORD_FINISH',
				name,
				recordId,
				error,
			});

			if (hasError && throwOnError) {
				throw error;
			}

			return deletedRecord;
		} finally {
			// If the record was deleted, invalidate the cache.
		}
	};

/**
 * Returns an action object that triggers an
 * edit to an entity record.
 *
 * @param {string}        name     Name of the edited entity record.
 * @param {number|string} recordId Record ID of the edited entity record.
 * @param {Object}        edits    The edits.
 *
 * @return {Object} Action object.
 */
export const editEntityRecord =
	(name, recordId, edits = {}) =>
	({ select, dispatch }) => {
		const entityConfig = select.getEntity(name);
		if (!entityConfig) {
			throw new Error(
				`The entity being edited (${name}) does not have a loaded config.`
			);
		}
		const { transientEdits = {}, mergedEdits = {} } = entityConfig;
		const record = select.getRawEntityRecord(name, recordId);
		const editedRecord = select.getEditedEntityRecord(name, recordId);

		const edit = {
			name,
			recordId,
			// Clear edits when they are equal to their persisted counterparts
			// so that the property is not considered dirty.
			edits: Object.keys(edits).reduce((acc, key) => {
				const recordValue = record[key];
				const editedRecordValue = editedRecord[key];
				const value = mergedEdits[key]
					? { ...editedRecordValue, ...edits[key] }
					: edits[key];
				acc[key] = fastDeepEqual(recordValue, value)
					? undefined
					: value;
				return acc;
			}, {}),
			transientEdits,
		};
		dispatch({
			type: 'EDIT_ENTITY_RECORD',
			...edit,
		});
	};

/**
 * Action triggered to save an entity record.
 *
 * @param {string}   name                         Name of the received entity.
 * @param {Object}   record                       Record to be saved.
 * @param {Object}   options                      Saving options.
 * @param {Function} [options.__unstableFetch]    Internal use only. Function to
 *                                                call instead of `apiFetch()`.
 *                                                Must return a promise.
 * @param {boolean}  [options.throwOnError=false] If false, this action suppresses all
 *                                                the exceptions. Defaults to false.
 */
export const saveEntityRecord =
	(name, record, { __unstableFetch = apiFetch, throwOnError = false } = {}) =>
	async ({ select, dispatch }) => {
		const entityConfig = await select.getEntity(name);
		if (!entityConfig) {
			throw `Could not find any entity named "${name}" please check entity config`;
		}
		const entityIdKey = entityConfig.key || DEFAULT_PRIMARY_KEY;
		const recordId = record[entityIdKey];

		try {
			// Evaluate optimized edits.
			// (Function edits that should be evaluated on save to avoid expensive computations on every edit.)
			for (const [key, value] of Object.entries(record)) {
				if (typeof value === 'function') {
					const evaluatedValue = value(
						select.getEditedEntityRecord(name, recordId)
					);
					dispatch.editEntityRecord(name, recordId, {
						[key]: evaluatedValue,
					});
					record[key] = evaluatedValue;
				}
			}

			dispatch({
				type: 'SAVE_ENTITY_RECORD_START',
				name,
				recordId,
			});
			let updatedRecord;
			let error;
			let hasError = false;
			try {
				const path = `${entityConfig.baseURL}${
					recordId ? '/' + recordId : ''
				}`;
				const persistedRecord = select.getRawEntityRecord(
					name,
					recordId
				);

				let edits = record;
				if (entityConfig.__unstablePrePersist) {
					edits = {
						...edits,
						...entityConfig.__unstablePrePersist(
							persistedRecord,
							edits
						),
					};
				}
				updatedRecord = await __unstableFetch({
					path,
					method: recordId ? 'PUT' : 'POST',
					data: edits,
				});
				dispatch.receiveEntityRecords(
					name,
					updatedRecord,
					undefined,
					true,
					edits
				);
			} catch (_error) {
				hasError = true;
				error = _error;
			}
			dispatch({
				type: 'SAVE_ENTITY_RECORD_FINISH',
				name,
				recordId,
				error,
			});

			if (hasError && throwOnError) {
				throw error;
			}

			return updatedRecord;
		} finally {
			// Clear the edit buffer.
		}
	};

/**
 * Action triggered to save an entity record's edits.
 *
 * @param {string} name     Name of the entity.
 * @param {Object} recordId ID of the record.
 * @param {Object} options  Saving options.
 */
export const saveEditedEntityRecord =
	(name, recordId, options) =>
	async ({ select, dispatch }) => {
		const entityConfig = await select.getEntity(name);
		if (!entityConfig) {
			throw `Could not find any entity named "${name}" please check entity config`;
		}
		const entityIdKey = entityConfig.key || DEFAULT_PRIMARY_KEY;

		const edits = select.getEntityRecordNonTransientEdits(name, recordId);
		const record = { [entityIdKey]: recordId, ...edits };
		return await dispatch.saveEntityRecord(name, record, options);
	};
