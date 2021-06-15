import {receiveItems, receiveQueriedItems, receiveItemTotal} from "../queried-data";
import {find, get, isEmpty, isEqual} from "lodash";
import { addQueryArgs } from '@wordpress/url';
import {entities} from "./entities";
import {dispatch, fetch, select} from "../controls";
import {STORE_NAME} from "./constants";
import {DEFAULT_ENTITY_KEY} from "./entities";


/**
 * Returns an action object used in adding new entities.
 *
 * @param {Array} entities  Entities received.
 *
 * @return {Object} Action object.
 */
export function addEntities( entities ) {
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
export function receiveEntityRecords( name, records, query, invalidateCache = false, edits) {
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
 * @param { string } name Entity Name
 * @param { number } total count
 * @param {Object} query
 * @param {Boolean} invalidateCache
 * @returns {{total: number, invalidateCache: boolean, query: ?Object, name, type: string}}
 */
export function receiveTotalEntityRecords( name, total, query, invalidateCache = false) {
	return {
		...receiveItemTotal(total, query),
		name,
		invalidateCache,
	};
}

/**
 * Returns an action object that triggers an
 * edit to an entity record.
 *
 * @param {string} name     Name of the edited entity record.
 * @param {string|Number} recordId Record ID of the edited entity record.
 * @param {Object} edits    The edits.
 * @param {Object} options  Options for the edit.
 * @param {boolean} options.undoIgnore Whether to ignore the edit in undo history or not.
 *
 * @return {Object} Action object.
 */
export function* editEntityRecord(name, recordId, edits, options = {}) {
	const entity = yield select( STORE_NAME, 'getEntity', name );
	if ( ! entity ) {
		throw new Error(
			`The entity being edited ${ name }) does not have a loaded config.`
		);
	}
	const { transientEdits = {}, mergedEdits = {} } = entity;
	const record = yield select( STORE_NAME, 'getRawEntityRecord', name, recordId);
	const editedRecord = yield select( STORE_NAME, 'getEditedEntityRecord', name, recordId);
	const edit = {
		name,
		recordId,
		// Clear edits when they are equal to their persisted counterparts
		// so that the property is not considered dirty.
		edits: Object.keys( edits ).reduce( ( acc, key ) => {
			const recordValue = record[ key ];
			const editedRecordValue = editedRecord[ key ];
			const value = mergedEdits[ key ]
				? { ...editedRecordValue, ...edits[ key ] }
				: edits[ key ];
			acc[ key ] = isEqual( recordValue, value ) ? undefined : value;
			return acc;
		}, {} ),
		transientEdits,
	};

	return {
		type: 'EDIT_ENTITY_RECORD',
		...edit,
	}
}

/**
 * Action triggered to save an entity record's edits.
 *
 * @param {string} name     Name of the entity.
 * @param {Object} recordId ID of the record.
 * @param {Object} options  Saving options.
 */
export function* saveEditedEntityRecord(name, recordId, options) {
	if (!(yield select(STORE_NAME, 'hasEditsForEntityRecord', name, recordId))) {
		return;
	}
	const edits = yield select(
		STORE_NAME,
		'getEntityRecordNonTransientEdits',
		name,
		recordId
	);
	const editsToSave = { id: recordId };
	for ( const edit in edits ) {
		if ( itemsToSave.some( ( item ) => item === edit ) ) {
			editsToSave[ edit ] = edits[ edit ];
		}
	}

	return yield* saveEntityRecord( name, editsToSave, options );
}

/**
 * Action triggered to save an entity record.
 *
 * @param {string}   name   Name of the received entity.
 * @param {Object}   record Record to be saved.
 * @param {Object} __unstableFetch  Internal use only. Function to call instead of `apiFetch()`.
 * Must return a control descriptor.
 */
export function* saveEntityRecord(name, record, __unstableFetch = null) {
	const entity = find( entities, { name } );
	if ( ! entity ) {
		throw (`Could not find any entity named "${name}" please check entity config`);
	}

	const entityIdKey = entity.key || DEFAULT_ENTITY_KEY;
	const recordId = record[entityIdKey];
	const invalidateCache = !isEmpty(recordId);
	for ( const [ key, value ] of Object.entries( record ) ) {
		if ( typeof value === 'function' ) {
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
					[ key ]: evaluatedValue,
				},
				{ undoIgnore: true }
			);
			record[ key ] = evaluatedValue;
		}
	}

	yield {
		type: 'SAVE_ENTITY_RECORD_START',
		name,
		recordId
	};
	let updatedRecord;
	let error;
	try {
		const path = `${ entity.endpoint }${
			recordId ? '/' + recordId : ''
		}`;
		let edits = record;
		const options = {
			path,
			method: recordId ? 'PUT' : 'POST',
			data: edits,
		};
		if ( __unstableFetch ) {
			updatedRecord = yield __unstableAwaitPromise(
				__unstableFetch( options )
			);
		} else {
			updatedRecord = yield apiFetch( options );
		}
		yield receiveEntityRecords(
			name,
			updatedRecord,
			undefined,
			invalidateCache,
			edits
		);
	} catch ( _error ) {
		error = _error;
	}
	yield {
		type: 'SAVE_ENTITY_RECORD_FINISH',
		name,
		recordId,
		error
	};

	return updatedRecord;
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
 *                                             call instead of `apiFetch()`.
 *                                             Must return a control descriptor.
 */
export function* deleteEntityRecord( name, recordId, query, { __unstableFetch = null } = {}) {
	const entity = find( entities, { name } );
	if ( ! entity ) {
		throw (`Could not find any entity named "${name}" please check entity config`);
	}
	let error;
	let deletedRecord = false;
	if (!entity) {
		return;
	}

	yield {
		type: 'DELETE_ENTITY_RECORD_START',
		name,
		recordId,
	};

	try {
		let path = `${entity.endpoint}/${recordId}`;

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
			deletedRecord = yield fetch(options);
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
