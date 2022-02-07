/**
 * External dependencies
 */
import { castArray, isEqual } from 'lodash';
/**
 * Internal dependencies
 */
import {
	receiveItems,
	receiveItemTotal,
	receiveQueriedItems,
	removeItems,
} from './queried-data';
import { select, dispatch } from './controls';
import { STORE_KEY } from './constants';
/**
 * WordPress dependencies
 */
import { addQueryArgs } from '@wordpress/url';
import { apiFetch } from '@wordpress/data-controls';
import { DEFAULT_ENTITY_KEY } from './entities';
import { createBatch } from './batch';

/**
 * Returns an action object used in signalling that authors have been received.
 *
 * @param {string}       queryID Query ID.
 * @param {Array|Object} users   Users received.
 *
 * @return {Object} Action object.
 */
export function receiveUserQuery( queryID, users ) {
	return {
		type: 'RECEIVE_USER_QUERY',
		users: castArray( users ),
		queryID,
	};
}

/**
 * Returns an action used in signalling that the current user has been received.
 *
 * @param {Object} currentUser Current user object.
 *
 * @return {Object} Action object.
 */
export function receiveCurrentUser( currentUser ) {
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
export function receiveUserPermission( key, isAllowed ) {
	return {
		type: 'RECEIVE_USER_PERMISSION',
		key,
		isAllowed,
	};
}

/**
 * Returns an action object used in adding new entities.
 *
 * @param {Array} entities Entities received.
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

export function receiveEntityTotal(
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
export function* deleteEntityRecord(
	name,
	recordId,
	query,
	{ __unstableFetch = null } = {}
) {
	const entity = yield select( STORE_KEY, 'getEntity', name );
	if ( ! entity ) {
		return;
	}
	let error;
	let deletedRecord = false;
	if ( ! entity ) {
		return;
	}

	// const lock = yield* __unstableAcquireStoreLock(
	// 	'ea/store',
	// 	[ 'entities', 'data', name, recordId ],
	// 	{ exclusive: true }
	// );
	try {
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
	} finally {
		// yield* __unstableReleaseStoreLock( lock );
	}
}

/**
 * Returns an action object that triggers an
 * edit to an entity record.
 *
 * @param {string}  name               Name of the edited entity record.
 * @param {number}  recordId           Record ID of the edited entity record.
 * @param {Object}  edits              The edits.
 * @param {Object}  options            Options for the edit.
 * @param {boolean} options.undoIgnore Whether to ignore the edit in undo history or not.
 *
 * @return {Object} Action object.
 */
export function* editEntityRecord( name, recordId, edits, options = {} ) {
	const entity = yield select( STORE_KEY, 'getEntity', name );
	if ( ! entity ) {
		throw new Error(
			`The entity being edited ${ name }), does not have a loaded config.`
		);
	}

	const { transientEdits = {}, mergedEdits = {} } = entity;
	const record = yield select(
		STORE_KEY,
		'getRawEntityRecord',
		name,
		recordId
	);

	const editedRecord = yield select(
		STORE_KEY,
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
		meta: {
			undo: ! options.undoIgnore && {
				...edit,
				// Send the current values for things like the first undo stack entry.
				edits: Object.keys( edits ).reduce( ( acc, key ) => {
					acc[ key ] = editedRecord[ key ];
					return acc;
				}, {} ),
			},
		},
	};
}

/**
 * Action triggered to save an entity record.
 *
 * @param {string} name            Name of the received entity.
 * @param {Object} record          Record to be saved.
 * @param {Object} __unstableFetch Internal use only. Function to
 *                                 call instead of `apiFetch()`.
 *                                 Must return a control
 *                                 descriptor.
 */
export function* saveEntityRecord( name, record, __unstableFetch = null ) {
	const entity = yield select( STORE_KEY, 'getEntity', name );
	if ( ! entity ) {
		return;
	}
	const entityIdKey = entity.key || DEFAULT_ENTITY_KEY;
	const recordId = record[ entityIdKey ];

	// const lock = yield* __unstableAcquireStoreLock(
	// 	'core',
	// 	[ 'entities', 'data', kind, name, recordId || uuid() ],
	// 	{ exclusive: true }
	// );
	try {
		// Evaluate optimized edits.
		// (Function edits that should be evaluated on save to avoid expensive computations on every edit.)
		for ( const [ key, value ] of Object.entries( record ) ) {
			if ( typeof value === 'function' ) {
				const evaluatedValue = value(
					yield select(
						STORE_KEY,
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
			const path = `${ entity.endpoint }${
				recordId ? '/' + recordId : ''
			}`;

			const persistedRecord = yield select(
				STORE_KEY,
				'getRawEntityRecord',
				name,
				recordId
			);
			let edits = record;
			if ( entity.__unstablePrePersist ) {
				edits = {
					...edits,
					...entity.__unstablePrePersist( persistedRecord, edits ),
				};
			}
			const options = {
				path,
				method: recordId ? 'PUT' : 'POST',
				data: edits,
			};
			if ( __unstableFetch ) {
				// updatedRecord = yield __unstableAwaitPromise(
				// 	__unstableFetch( options )
				// );
			} else {
				updatedRecord = yield apiFetch( options );
			}
			yield receiveEntityRecords(
				name,
				updatedRecord,
				undefined,
				true,
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
	} finally {
		// yield* __unstableReleaseStoreLock( lock );
	}
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
			STORE_KEY,
			'hasEditsForEntityRecord',
			name,
			recordId
		) )
	) {
		return;
	}
	const edits = yield select(
		STORE_KEY,
		'getEntityRecordNonTransientEdits',
		name,
		recordId
	);

	const record = { id: recordId, ...edits };
	return yield saveEntityRecord( name, record, options );
}

/**
 * Runs multiple core-data actions at the same time using one API request.
 *
 * Example:
 *
 * ```
 * const [ savedRecord, updatedRecord, deletedRecord ] =
 *   await dispatch( 'ea/data' ).batchRequest( [
 *     ( { saveEntityRecord } ) => saveEntityRecord( 'account', 10 ),
 *     ( { saveEditedEntityRecord } ) => saveEntityRecord( 'account', 123 ),
 *     ( { deleteEntityRecord } ) => deleteEntityRecord( 'account', 123, null ),
 *   ] );
 * ```
 *
 * @param {Array} requests Array of functions which are invoked simultaneously.
 *                         Each function is passed an object containing
 *                         `saveEntityRecord`, `saveEditedEntityRecord`, and
 *                         `deleteEntityRecord`.
 *
 * @return {Promise} A promise that resolves to an array containing the return
 *                   values of each function given in `requests`.
 */
export function* batchRequest( requests ) {
	const batch = createBatch();
	const api = {
		saveEntityRecord( kind, name, record, options ) {
			return batch.add( ( add ) =>
				dispatch( STORE_KEY ).saveEntityRecord( name, record, {
					...options,
					__unstableFetch: add,
				} )
			);
		},
		saveEditedEntityRecord( kind, name, recordId, options ) {
			return batch.add( ( add ) =>
				dispatch( STORE_KEY ).saveEditedEntityRecord( name, recordId, {
					...options,
					__unstableFetch: add,
				} )
			);
		},
		deleteEntityRecord( name, recordId, query, options ) {
			return batch.add( ( add ) =>
				dispatch( STORE_KEY ).deleteEntityRecord(
					name,
					recordId,
					query,
					{
						...options,
						__unstableFetch: add,
					}
				)
			);
		},
	};
	const resultPromises = requests.map( ( request ) => request( api ) );
	const [ , ...results ] = yield select(
		Promise.all( [ batch.run(), ...resultPromises ] )
	);
	return results;
}
