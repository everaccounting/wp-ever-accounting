/**
 * External dependencies
 */
import { isEmpty, isEqual } from 'lodash';

/**
 * WordPress dependencies
 */
// eslint-disable-next-line import/no-extraneous-dependencies
import { addQueryArgs } from '@wordpress/url';
/**
 * Internal dependencies
 */
import { DEFAULT_ENTITY_KEY } from '../entities';
import { dispatch, apiFetch, select, awaitPromise } from '../controls';
import { STORE_NAME } from '../constants';
import {
	receiveItems,
	removeItems,
	receiveQueriedItems,
	receiveItemTotal,
	receiveError,
} from '../queried-data';

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
export function receiveEntityRecords(
	name,
	records,
	query,
	invalidateCache = false,
	edits
) {
	let action;
	if ( query ) {
		action = receiveQueriedItems( records, query, edits );
	} else {
		action = receiveItems( records, edits );
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
		...receiveItemTotal( total, query ),
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
export function receiveEntityError( name, error, query ) {
	return {
		...receiveError( error, query ),
		name,
	};
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
export function* editEntityRecord( name, recordId, edits ) {
	const entity = yield select( STORE_NAME, 'getEntity', name );
	if ( ! entity ) {
		throw new Error(
			`The entity being edited ${ name }) does not have a loaded config.`
		);
	}
	const { transientEdits = {}, mergedEdits = {} } = entity;
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
	};
}

/**
 * Action triggered to save an entity record's edits.
 *
 * @param {string} name     Name of the entity.
 * @param {Object} recordId ID of the record.
 * @param {Object} options  Saving options.
 */
export function* saveEditedEntityRecord( name, recordId, options ) {
	if (
		! ( yield select(
			STORE_NAME,
			'hasEditsForEntityRecord',
			name,
			recordId
		) )
	) {
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
		if ( editsToSave.some( ( item ) => item === edit ) ) {
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
 * @param {Object} customRequest  Internal use only. Function to call instead of `fetch()`.
 * Must return a control descriptor.
 */
export function* saveEntityRecord( name, record, customRequest = null ) {
	const entity = yield select( STORE_NAME, 'getEntity', name );

	if ( ! entity ) {
		throw `Could not find any entity named "${ name }" please check entity config`;
	}

	const entityIdKey = entity.key || DEFAULT_ENTITY_KEY;
	const recordId = record[ entityIdKey ];
	const invalidateCache = ! isEmpty( recordId );
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
		recordId,
	};
	let updatedRecord;
	let error;

	try {
		const path = `${ entity.endpoint }${ recordId ? '/' + recordId : '' }`;
		const edits = record;
		const options = {
			path,
			method: recordId ? 'PUT' : 'POST',
			data: edits,
		};
		if ( customRequest ) {
			updatedRecord = yield awaitPromise( customRequest( options ) );
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
		error,
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
 *                                             call instead of `fetch()`.
 *                                             Must return a control descriptor.
 */
export function* deleteEntityRecord(
	name,
	recordId,
	query,
	{ __unstableFetch = null } = {}
) {
	const entity = yield select( STORE_NAME, 'getEntity', name );
	if ( ! entity ) {
		throw `Could not find any entity named "${ name }" please check entity config`;
	}
	let error;
	let deletedRecord = false;
	if ( ! entity ) {
		return;
	}

	yield {
		type: 'DELETE_ENTITY_RECORD_START',
		name,
		recordId,
	};

	try {
		let path = `${ entity.endpoint }/${ recordId }`;

		if ( query ) {
			path = addQueryArgs( path, query );
		}

		const options = {
			path,
			method: 'DELETE',
		};

		if ( __unstableFetch ) {
			deletedRecord = yield dispatch( __unstableFetch( options ) );
		} else {
			deletedRecord = yield apiFetch( options );
		}

		yield removeItems( name, recordId, true );
	} catch ( _error ) {
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
