/**
 * External dependencies
 */
import { castArray, get, isEqual, find } from 'lodash';
import { v4 as uuid } from 'uuid';

/**
 * WordPress dependencies
 */
import { controls } from '@wordpress/data';
import { apiFetch, __unstableAwaitPromise } from '@wordpress/data-controls';
import { addQueryArgs } from '@wordpress/url';

/**
 * Internal dependencies
 */
import { receiveItems, removeItems, receiveQueriedItems } from './queried-data';
import { getKindEntities, DEFAULT_ENTITY_KEY } from './entities';
import {
	__unstableAcquireStoreLock,
	__unstableReleaseStoreLock,
} from './locks';
import { createBatch } from './batch';
import { getDispatch } from './controls';


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
 * @param {string}       kind            Kind of the received entity.
 * @param {string}       name            Name of the received entity.
 * @param {Array|Object} records         Records received.
 * @param {?Object}      query           Query Object.
 * @param {?boolean}     invalidateCache Should invalidate query caches.
 * @param {?Object}      edits           Edits to reset.
 * @return {Object} Action object.
 */
export function receiveEntityRecords(
	kind,
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
	console.log(action);
	return {
		...action,
		kind,
		name,
		invalidateCache,
	};
}

/**
 * Action triggered to delete an entity record.
 *
 * @param {string}   kind                      Kind of the deleted entity.
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
	kind,
	name,
	recordId,
	query,
	{ __unstableFetch = null } = {}
) {
	const entities = yield getKindEntities( kind );
	const entity = find( entities, { kind, name } );
	let error;
	let deletedRecord = false;
	if ( ! entity ) {
		return;
	}

	const lock = yield* __unstableAcquireStoreLock(
		'ea/store',
		[ 'entities', 'data', kind, name, recordId ],
		{ exclusive: true }
	);
	try {
		yield {
			type: 'DELETE_ENTITY_RECORD_START',
			kind,
			name,
			recordId,
		};

		try {
			let path = `${ entity.baseURL }/${ recordId }`;

			if ( query ) {
				path = addQueryArgs( path, query );
			}

			const options = {
				path,
				method: 'DELETE',
			};
			if ( __unstableFetch ) {
				deletedRecord = yield __unstableAwaitPromise(
					__unstableFetch( options )
				);
			} else {
				deletedRecord = yield apiFetch( options );
			}

			yield removeItems( kind, name, recordId, true );
		} catch ( _error ) {
			error = _error;
		}

		yield {
			type: 'DELETE_ENTITY_RECORD_FINISH',
			kind,
			name,
			recordId,
			error,
		};

		return deletedRecord;
	} finally {
		yield* __unstableReleaseStoreLock( lock );
	}
}

/**
 * Returns an action object that triggers an
 * edit to an entity record.
 *
 * @param {string} kind     Kind of the edited entity record.
 * @param {string} name     Name of the edited entity record.
 * @param {number} recordId Record ID of the edited entity record.
 * @param {Object} edits    The edits.
 * @param {Object} options  Options for the edit.
 * @param {boolean} options.undoIgnore Whether to ignore the edit in undo history or not.
 *
 * @return {Object} Action object.
 */
export function* editEntityRecord( kind, name, recordId, edits, options = {} ) {
	const entity = yield controls.select( 'ea/store', 'getEntity', kind, name );
	if ( ! entity ) {
		throw new Error(
			`The entity being edited (${ kind }, ${ name }) does not have a loaded config.`
		);
	}
	const { transientEdits = {}, mergedEdits = {} } = entity;
	const record = yield controls.select(
		'ea/store',
		'getRawEntityRecord',
		kind,
		name,
		recordId
	);
	const editedRecord = yield controls.select(
		'ea/store',
		'getEditedEntityRecord',
		kind,
		name,
		recordId
	);

	const edit = {
		kind,
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
 * @param {string}   kind                       Kind of the received entity.
 * @param {string}   name                       Name of the received entity.
 * @param {Object}   record                     Record to be saved.
 * @param {Object}   options                    Saving options.
 * @param {boolean}  [options.isAutosave=false] Whether this is an autosave.
 * @param {Function} [options.__unstableFetch]  Internal use only. Function to
 *                                              call instead of `apiFetch()`.
 *                                              Must return a control
 *                                              descriptor.
 */
export function* saveEntityRecord(
	kind,
	name,
	record,
	{ isAutosave = false, __unstableFetch = null } = {}
) {
	const entities = yield getKindEntities( kind );
	const entity = find( entities, { kind, name } );
	if ( ! entity ) {
		return;
	}
	const entityIdKey = entity.key || DEFAULT_ENTITY_KEY;
	const recordId = record[ entityIdKey ];

	const lock = yield* __unstableAcquireStoreLock(
		'ea/store',
		[ 'entities', 'data', kind, name, recordId || uuid() ],
		{ exclusive: true }
	);
	try {
		// Evaluate optimized edits.
		// (Function edits that should be evaluated on save to avoid expensive computations on every edit.)
		for ( const [ key, value ] of Object.entries( record ) ) {
			if ( typeof value === 'function' ) {
				const evaluatedValue = value(
					yield controls.select(
						'ea/store',
						'getEditedEntityRecord',
						kind,
						name,
						recordId
					)
				);
				yield editEntityRecord(
					kind,
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
			kind,
			name,
			recordId,
			isAutosave,
		};
		let updatedRecord;
		let error;
		try {
			const path = `${ entity.baseURL }${
				recordId ? '/' + recordId : ''
			}`;
			const persistedRecord = yield controls.select(
				'ea/store',
				'getRawEntityRecord',
				kind,
				name,
				recordId
			);

			if ( isAutosave ) {
				// Most of this autosave logic is very specific to posts.
				// This is fine for now as it is the only supported autosave,
				// but ideally this should all be handled in the back end,
				// so the client just sends and receives objects.
				const currentUser = yield controls.select(
					'ea/store',
					'getCurrentUser'
				);
				const currentUserId = currentUser ? currentUser.id : undefined;
				const autosavePost = yield controls.select(
					'ea/store',
					'getAutosave',
					persistedRecord.type,
					persistedRecord.id,
					currentUserId
				);
				// Autosaves need all expected fields to be present.
				// So we fallback to the previous autosave and then
				// to the actual persisted entity if the edits don't
				// have a value.
				let data = { ...persistedRecord, ...autosavePost, ...record };
				data = Object.keys( data ).reduce(
					( acc, key ) => {
						if (
							[ 'title', 'excerpt', 'content' ].includes( key )
						) {
							// Edits should be the "raw" attribute values.
							acc[ key ] = get( data[ key ], 'raw', data[ key ] );
						}
						return acc;
					},
					{
						status:
							data.status === 'auto-draft'
								? 'draft'
								: data.status,
					}
				);
				const options = {
					path: `${ path }/autosaves`,
					method: 'POST',
					data,
				};
				if ( __unstableFetch ) {
					updatedRecord = yield __unstableAwaitPromise(
						__unstableFetch( options )
					);
				} else {
					updatedRecord = yield apiFetch( options );
				}
				// An autosave may be processed by the server as a regular save
				// when its update is requested by the author and the post had
				// draft or auto-draft status.
				if ( persistedRecord.id === updatedRecord.id ) {
					let newRecord = {
						...persistedRecord,
						...data,
						...updatedRecord,
					};
					newRecord = Object.keys( newRecord ).reduce(
						( acc, key ) => {
							// These properties are persisted in autosaves.
							if (
								[ 'title', 'excerpt', 'content' ].includes(
									key
								)
							) {
								// Edits should be the "raw" attribute values.
								acc[ key ] = get(
									newRecord[ key ],
									'raw',
									newRecord[ key ]
								);
							} else if ( key === 'status' ) {
								// Status is only persisted in autosaves when going from
								// "auto-draft" to "draft".
								acc[ key ] =
									persistedRecord.status === 'auto-draft' &&
									newRecord.status === 'draft'
										? newRecord.status
										: persistedRecord.status;
							} else {
								// These properties are not persisted in autosaves.
								acc[ key ] = get(
									persistedRecord[ key ],
									'raw',
									persistedRecord[ key ]
								);
							}
							return acc;
						},
						{}
					);
					yield receiveEntityRecords(
						kind,
						name,
						newRecord,
						undefined,
						true
					);
				} else {
					yield receiveAutosaves( persistedRecord.id, updatedRecord );
				}
			} else {
				let edits = record;
				if ( entity.__unstablePrePersist ) {
					edits = {
						...edits,
						...entity.__unstablePrePersist(
							persistedRecord,
							edits
						),
					};
				}
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
					kind,
					name,
					updatedRecord,
					undefined,
					true,
					edits
				);
			}
		} catch ( _error ) {
			error = _error;
		}
		yield {
			type: 'SAVE_ENTITY_RECORD_FINISH',
			kind,
			name,
			recordId,
			error,
			isAutosave,
		};

		return updatedRecord;
	} finally {
		yield* __unstableReleaseStoreLock( lock );
	}
}

/**
 * Runs multiple core-data actions at the same time using one API request.
 *
 * Example:
 *
 * ```
 * const [ savedRecord, updatedRecord, deletedRecord ] =
 *   await dispatch( 'ea/store' ).__experimentalBatch( [
 *     ( { saveEntityRecord } ) => saveEntityRecord( 'root', 'widget', widget ),
 *     ( { saveEditedEntityRecord } ) => saveEntityRecord( 'root', 'widget', 123 ),
 *     ( { deleteEntityRecord } ) => deleteEntityRecord( 'root', 'widget', 123, null ),
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
export function* __experimentalBatch( requests ) {
	const batch = createBatch();
	const dispatch = yield getDispatch();
	const api = {
		saveEntityRecord( kind, name, record, options ) {
			return batch.add( ( add ) =>
				dispatch( 'ea/store' ).saveEntityRecord( kind, name, record, {
					...options,
					__unstableFetch: add,
				} )
			);
		},
		saveEditedEntityRecord( kind, name, recordId, options ) {
			return batch.add( ( add ) =>
				dispatch( 'ea/store' ).saveEditedEntityRecord(
					kind,
					name,
					recordId,
					{
						...options,
						__unstableFetch: add,
					}
				)
			);
		},
		deleteEntityRecord( kind, name, recordId, query, options ) {
			return batch.add( ( add ) =>
				dispatch( 'ea/store' ).deleteEntityRecord(
					kind,
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
	const [ , ...results ] = yield __unstableAwaitPromise(
		Promise.all( [ batch.run(), ...resultPromises ] )
	);
	return results;
}

/**
 * Action triggered to save an entity record's edits.
 *
 * @param {string} kind     Kind of the entity.
 * @param {string} name     Name of the entity.
 * @param {Object} recordId ID of the record.
 * @param {Object} options  Saving options.
 */
export function* saveEditedEntityRecord( kind, name, recordId, options ) {
	if (
		! ( yield controls.select(
			'ea/store',
			'hasEditsForEntityRecord',
			kind,
			name,
			recordId
		) )
	) {
		return;
	}
	const edits = yield controls.select(
		'ea/store',
		'getEntityRecordNonTransientEdits',
		kind,
		name,
		recordId
	);
	const record = { id: recordId, ...edits };
	return yield* saveEntityRecord( kind, name, record, options );
}
