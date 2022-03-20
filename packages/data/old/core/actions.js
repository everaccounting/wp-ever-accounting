/**
 * Internal dependencies
 */
import {
	awaitPromise,
	select,
	dispatch,
	apiFetch,
	resolveDispatch,
} from '../base-controls';
import { STORE_NAME } from './constants';
/**
 * External dependencies
 */
import { castArray, isEmpty } from 'lodash';
import {
	receiveItems,
	receiveQueriedItems,
	receiveItemTotal,
	receiveError,
	removeItems,
} from './queried-data';
/**
 * WordPress dependencies
 */
import { addQueryArgs } from '@wordpress/url';
import { API_NAMESPACE } from '../site-data';
import { getEntity } from './entities';

/**
 * Returns an action object used in adding new entities.
 *
 * @param {Array|Object} schema Entities received.
 *
 * @return {Object} Action object.
 */
export function receiveEntity(schema) {
	return {
		type: 'RECEIVE_ENTITY',
		schema: castArray(schema),
	};
}

/**
 * Returns an action object used in signalling that entity records have been received.
 *
 * @param {string}       name            Name of the received entity.
 * @param {Array|Object} records         Records received.
 * @param {?Object}      query           Query Object.
 * @param {?string}		 primaryKey   Primary key of the item.
 * @param {?boolean}     invalidateCache Should invalidate query caches.
 * @param {?Object}      edits           Edits to reset.
 * @return {Object} Action object.
 */
export function receiveEntityRecords(
	name,
	records,
	query,
	primaryKey,
	invalidateCache = false,
	edits = {}
) {
	let action;
	if (query) {
		action = receiveQueriedItems(records, query, primaryKey, edits);
	} else {
		action = receiveItems(records, primaryKey, edits);
	}

	return {
		...action,
		name,
		invalidateCache,
	};
}

/**
 *
 * @param { string } name Entity Name
 * @param { number } total count
 * @param {Object} query
 * @param {boolean} invalidateCache
 * @return {{total: number, invalidateCache: boolean, query: ?Object, name, type: string}} Receive Total.
 */
export function receiveTotalEntityRecords(
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
 * Receive entity error.
 *
 * @param {string} name
 * @param {Object} error
 * @param {Object} query
 * @return {{query: ?Object, type: string, error: Object}} Receive error.
 */
export function receiveEntityError(name, error, query) {
	return {
		...receiveError(error, query),
		name,
	};
}

/**
 * Action triggered to delete an entity record.
 *
 * @param {string}   name                      Name of the deleted entity.
 * @param {string}   recordId                  Record ID of the deleted entity.
 * @param {?Object}  query                     Special query parameters for the
 *                                             DELETE API call.
 * @param {Object}   [options]                 Delete options.
 * @param {Function} [options.__unstableFetch] Internal use only. Function to
 *                                             call instead of `fetch()`.
 *                                             Must return a control descriptor.
 */
export function* deleteEntityRecord(
	name,
	recordId,
	query,
	{ __unstableFetch = null } = {}
) {
	const entity = yield getEntity(name);

	if (!entity) {
		throw `Could not find any entity named "${name}" please check entity config`;
	}
	const { route } = entity;
	let deletedRecord = false;
	let error;
	yield {
		type: 'DELETE_ENTITY_RECORD_START',
		name,
		recordId,
	};

	try {
		let path = `${route}/${recordId}`;

		if (query) {
			path = addQueryArgs(path, query);
		}

		const options = {
			path,
			method: 'DELETE',
		};

		if (__unstableFetch) {
			deletedRecord = yield dispatch(__unstableFetch(options));
		} else {
			deletedRecord = yield apiFetch(options);
		}

		yield removeItems(name, recordId, true);
	} catch (_error) {
		error = _error;
	}

	yield {
		type: 'DELETE_ENTITY_RECORD_FINISH',
		name,
		recordId,
		error,
	};

	return deletedRecord;
}

/**
 * Action triggered to save an entity record.
 *
 * @param {string}   name   Name of the received entity.
 * @param {Object}   record Record to be saved.
 * @param {Object} customRequest  Internal use only. Function to call instead of `fetch()`.
 * Must return a control descriptor.
 */
export function* saveEntityRecord(name, record, customRequest = null) {
	const entity = yield getEntity(name);

	if (!entity) {
		throw `Could not find any entity named "${name}" please check entity config`;
	}
	const { route, primaryKey } = entity;

	const recordId = record[primaryKey];
	const invalidateCache = !isEmpty(recordId);
	for (const [key, value] of Object.entries(record)) {
		if (typeof value === 'function') {
			const evaluatedValue = value(
				yield select(
					STORE_NAME,
					'getEditedEntityRecord',
					name,
					recordId
				)
			);
			yield editEntityRecord(
				name,
				recordId,
				{
					[key]: evaluatedValue,
				},
				{ undoIgnore: true }
			);
			record[key] = evaluatedValue;
		}
	}

	yield {
		type: 'SAVE_ENTITY_RECORD_START',
		name,
		recordId,
	};
	let updatedRecord;
	let error;

	try {
		const path = `${route}${recordId ? '/' + recordId : ''}`;
		const edits = record;
		const options = {
			path,
			method: recordId ? 'PUT' : 'POST',
			data: edits,
		};
		if (customRequest) {
			updatedRecord = yield awaitPromise(customRequest(options));
		} else {
			updatedRecord = yield apiFetch(options);
		}
		yield receiveEntityRecords(
			name,
			updatedRecord,
			undefined,
			primaryKey,
			invalidateCache,
			edits
		);
	} catch (_error) {
		error = _error;
	}
	yield {
		type: 'SAVE_ENTITY_RECORD_FINISH',
		name,
		recordId,
		error,
	};

	return updatedRecord;
}

/**
 * Returns an action object that triggers an
 * edit to an entity record.
 *
 * @param {string} name     Name of the edited entity record.
 * @param {string | number} recordId Record ID of the edited entity record.
 * @param {Object} edits    The edits.
 *
 * @return {Object} Action object.
 */
export function* editEntityRecord(name, recordId, edits) {
	const record = yield select(
		STORE_NAME,
		'getRawEntityRecord',
		name,
		recordId
	);
	const editedRecord = yield select(
		STORE_NAME,
		'getEditedEntityRecord',
		name,
		recordId
	);
	const edit = {
		name,
		recordId,
		// Clear edits when they are equal to their persisted counterparts
		// so that the property is not considered dirty.
		edits: {
			...record,
			...editedRecord,
			...edits,
		},
	};

	return {
		type: 'EDIT_ENTITY_RECORD',
		...edit,
	};
}

/**
 * Receive settings.
 *
 * @param {Object} settings
 * @param {Object} error
 * @param {Object} time
 * @return {{settings, time: Date, type: string, error: null}} Settings update action.
 */
export function receiveSettings(settings, error = null, time = new Date()) {
	return {
		type: 'RECEIVE_SETTINGS',
		settings,
		error,
		time,
	};
}

/**
 * Update options
 *
 * @param {Object} data
 * @return {Object}
 */
export function* updateSettings(data) {
	try {
		const settings = yield apiFetch({
			path: API_NAMESPACE + '/settings',
			method: 'POST',
			data,
		});
		yield receiveSettings(settings);
		return { success: true, ...settings };
	} catch (error) {
		yield receiveSettings({}, error);
		return { success: false, ...error };
	}
}

/**
 * Update option.
 *
 * @param {number} id
 * @param {string} value
 * @return {Object} Resolver.
 */
export function* updateOption(id, value) {
	try {
		const settings = yield apiFetch({
			path: API_NAMESPACE + '/settings/' + id,
			method: 'PUT',
			data: { id, value },
		});

		yield receiveSettings({ settings: [settings] });
		return { success: true, ...settings };
	} catch (error) {
		yield receiveSettings({}, error);
		return { success: false, ...error };
	}
}

/**
 * Returns an action used in signalling that the current user has been received.
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
 * Returns an action object used in signalling that the current user has
 * permission to perform an action on a REST resource.
 *
 * @param {string}  key       A key that represents the action and REST resource.
 * @param {boolean} isAllowed Whether or not the user can perform the action.
 *
 * @return {Object} Action object.
 */
export function receiveUserPermission(key, isAllowed) {
	return {
		type: 'RECEIVE_USER_PERMISSION',
		key,
		isAllowed,
	};
}

export function* createNotice(type, notice) {
	yield resolveDispatch('core/notices', 'createNotice', type, notice);
}
