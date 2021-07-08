/**
 * Internal dependencies
 */
import { EMPTY_ARRAY } from '../constants';
/**
 * External dependencies
 */
import { find, get, isEmpty, set } from 'lodash';
import createSelector from 'rememo';
import {
	getQueriedError,
	getQueriedItems,
	getQueriedTotal,
} from '../queried-data';
import { getNormalizedCommaSeparable } from '../utils';
import { DEFAULT_ENTITY_KEY } from '../entities';

/**
 * Returns the entity object from given name.
 *
 * @param {Object} state   Data state.
 * @param {string} name  Entity name.
 *
 * @return {Object} Entity
 */
export function getEntity( state, name ) {
	return find( state.entities.config, { name } );
}

/**
 * Returns the Entity's records.
 *
 * @param {Object}  state State tree
 * @param {string}  name  Entity name.
 * @param {string}  key   Primary key.
 * @param {?Object} query Optional terms query.
 *
 * @return {?Array} Records.
 */
export function getEntityRecord( state, name, key = '', query = {} ) {
	const queriedState = get( state.entities.data, [ name, 'queriedData' ] );
	if ( ! queriedState ) {
		return undefined;
	}

	if ( query === undefined ) {
		// If expecting a complete item, validate that completeness.
		if ( ! queriedState.itemIsComplete[ key ] ) {
			return undefined;
		}

		return queriedState.items[ key ];
	}

	const item = queriedState.items[ key ];
	if ( item && query._fields ) {
		const filteredItem = {};
		const fields = getNormalizedCommaSeparable( query._fields );
		for ( let f = 0; f < fields.length; f++ ) {
			const field = fields[ f ].split( '.' );
			const value = get( item, field );
			set( filteredItem, field, value );
		}
		return filteredItem;
	}

	return item;
}

/**
 * Returns the entity's record object by key,
 * with its attributes mapped to their raw values.
 *
 * @param {Object} state  State tree.
 * @param {string} name   Entity name.
 * @param {number} key    Record's key.
 *
 * @return {Array} Object with the entity's raw attributes.
 */
export const getRawEntityRecord = createSelector(
	( state, name, key ) => {
		const record = getEntityRecord( state, name, key );
		return (
			record &&
			Object.keys( record ).reduce( ( accumulator, _key ) => {
				// Because edits are the "raw" attribute values,
				// we return those from record selectors to make rendering,
				// comparisons, and joins with edits easier.
				accumulator[ _key ] = get(
					record[ _key ],
					'raw',
					record[ _key ]
				);
				return accumulator;
			}, {} )
		);
	},
	( state ) => [ state.data ]
);

/**
 * Returns true if records have been received for the given set of parameters,
 * or false otherwise.
 *
 * @param {Object}  state State tree
 * @param {string}  name  Entity name.
 * @param {?Object} query Optional terms query.
 *
 * @return {boolean} Whether entity records have been received.
 */
export function hasEntityRecords( state, name, query = {} ) {
	const records = getEntityRecords( state, name, query );
	return Array.isArray( records ) && ! isEmpty( records );
}

/**
 * Returns the Entity's records.
 *
 * @param {Object}  state State tree
 * @param {string}  name  Entity name.
 * @param {?Object} query Optional terms query.
 *
 * @return {?Array} Records.
 */
export function getEntityRecords( state, name, query ) {
	// Queried data state is populated for all known entities. If this is not
	// assigned for the given parameters, then it is known to not exist. Thus, a
	// return value of an empty array is used instead of `null` (where `null` is
	// otherwise used to represent an unknown state).
	const queriedState = get( state.entities.data, [ name, 'queriedData' ] );
	if ( ! queriedState ) {
		return EMPTY_ARRAY;
	}
	const items = getQueriedItems( queriedState, query );
	if ( ! items ) {
		return EMPTY_ARRAY;
	}
	return items;
}

/**
 * Returns the Entity's records.
 *
 * @param {Object}  state State tree
 * @param {string}  name  Entity name.
 * @param {?Object} query Optional terms query.
 *
 * @param {Array} defaults Default value.
 * @return {number} Record Count.
 */
export function getTotalEntityRecords(
	state,
	name,
	query = {},
	defaults = undefined
) {
	// Queried data state is populated for all known entities. If this is not
	// assigned for the given parameters, then it is known to not exist. Thus, a
	// return value of an empty array is used instead of `null` (where `null` is
	// otherwise used to represent an unknown state).
	const queriedState = get( state.entities.data, [ name, 'queriedData' ] );
	if ( ! queriedState ) {
		return defaults;
	}
	return getQueriedTotal( queriedState, query );
}

/**
 * Returns the  list of dirty entity records.
 *
 * @param {Object} state State tree.
 *
 * @return {[{ title: string, key: string, name: string, kind: string }]} The list of updated records
 */
export const getDirtyEntityRecords = createSelector(
	( state ) => {
		const {
			entities: { data },
		} = state;
		const dirtyRecords = [];
		Object.keys( data ).forEach( ( kind ) => {
			Object.keys( data[ kind ] ).forEach( ( name ) => {
				const primaryKeys = Object.keys(
					data[ kind ][ name ].edits
				).filter( ( primaryKey ) =>
					hasEditsForEntityRecord( state, kind, name, primaryKey )
				);

				if ( primaryKeys.length ) {
					const entity = getEntity( state, kind, name );
					primaryKeys.forEach( ( primaryKey ) => {
						const entityRecord = getEditedEntityRecord(
							state,
							kind,
							name,
							primaryKey
						);
						dirtyRecords.push( {
							// We avoid using primaryKey because it's transformed into a string
							// when it's used as an object key.
							key:
								entityRecord[
									entity.key || DEFAULT_ENTITY_KEY
								],
							title: entity?.getTitle?.( entityRecord ) || '',
							name,
							kind,
						} );
					} );
				}
			} );
		} );

		return dirtyRecords;
	},
	( state ) => [ state.entities.data ]
);

/**
 * Returns the specified entity record's edits.
 *
 * @param {Object} state    State tree.
 * @param {string} name     Entity name.
 * @param {number} recordId Record ID.
 *
 * @return {Object?} The entity record's edits.
 */
export function getEntityRecordEdits( state, name, recordId ) {
	return get( state.entities.data, [ name, 'edits', recordId ] );
}

/**
 * Returns the specified entity record's non transient edits.
 *
 * Transient edits don't create an undo level, and
 * are not considered for change detection.
 * They are defined in the entity's config.
 *
 * @param {Object} state    State tree.
 * @param {string} name     Entity name.
 * @param {number} recordId Record ID.
 *
 * @return {Object?} The entity record's non transient edits.
 */
export const getEntityRecordNonTransientEdits = createSelector(
	( state, name, recordId ) => {
		const { transientEdits } = getEntity( state, name ) || {};
		const edits = getEntityRecordEdits( state, name, recordId ) || {};
		if ( ! transientEdits ) {
			return edits;
		}
		return Object.keys( edits ).reduce( ( acc, key ) => {
			if ( ! transientEdits[ key ] ) {
				acc[ key ] = edits[ key ];
			}
			return acc;
		}, {} );
	},
	( state ) => [ state.entities.config, state.entities.data ]
);

/**
 * Returns true if the specified entity record has edits,
 * and false otherwise.
 *
 * @param {Object} state    State tree.
 * @param {string} name     Entity name.
 * @param {number} recordId Record ID.
 *
 * @return {boolean} Whether the entity record has edits or not.
 */
export function hasEditsForEntityRecord( state, name, recordId ) {
	return (
		isSavingEntityRecord( state, name, recordId ) ||
		Object.keys( getEntityRecordNonTransientEdits( state, name, recordId ) )
			.length > 0
	);
}

/**
 * Returns the specified entity record, merged with its edits.
 *
 * @param {Object} state    State tree.
 * @param {string} name     Entity name.
 * @param {number} recordId Record ID.
 *
 * @return {Object?} The entity record, merged with its edits.
 */
export const getEditedEntityRecord = createSelector(
	( state, name, recordId ) => ( {
		...getRawEntityRecord( state, name, recordId ),
		...getEntityRecordEdits( state, name, recordId ),
	} ),
	( state ) => [ state.entities.data ]
);

/**
 * Returns true if the specified entity record is saving, and false otherwise.
 *
 * @param {Object} state    State tree.
 * @param {string} name     Entity name.
 * @param {number} recordId Record ID.
 *
 * @return {boolean} Whether the entity record is saving or not.
 */
export function isSavingEntityRecord( state, name, recordId ) {
	return get(
		state.entities.data,
		[ name, 'saving', recordId, 'pending' ],
		false
	);
}

/**
 * Returns true if the specified entity record is deleting, and false otherwise.
 *
 * @param {Object} state    State tree.
 * @param {string} name     Entity name.
 * @param {number} recordId Record ID.
 *
 * @return {boolean} Whether the entity record is deleting or not.
 */
export function isDeletingEntityRecord( state, name, recordId ) {
	return get(
		state.entities.data,
		[ name, 'deleting', recordId, 'pending' ],
		false
	);
}

/**
 * Returns the specified entity record's last save error.
 *
 * @param {Object} state    State tree.
 * @param {string} name     Entity name.
 * @param {number} recordId Record ID.
 *
 * @return {Object?} The entity record's save error.
 */
export function getLastEntitySaveError( state, name, recordId ) {
	return get( state.entities.data, [ name, 'saving', recordId, 'error' ] );
}

/**
 * Returns the specified entity record's last delete error.
 *
 * @param {Object} state    State tree.
 * @param {string} name     Entity name.
 * @param {number} recordId Record ID.
 *
 * @return {Object?} The entity record's save error.
 */
export function getLastEntityDeleteError( state, name, recordId ) {
	return get( state.entities.data, [ name, 'deleting', recordId, 'error' ] );
}

/**
 * Returns the Entity record error.
 *
 * @param {Object}  state State tree
 * @param {string}  name  Entity name.
 * @param {number | string} recordId Key.
 * @param {?Object} query Optional terms query.
 *
 * @return {Object} Error.
 */
export function getEntityRecordError( state, name, recordId, query = {} ) {
	const queriedState = get( state.entities.data, [
		name,
		'queriedData',
		'errors',
	] );
	return getQueriedError( queriedState, { ...query, key: recordId } );
}

/**
 * Returns the Entity record error.
 *
 * @param {Object}  state State tree
 * @param {string}  name  Entity name.
 * @param {?Object} query Optional terms query.
 *
 * @return {Object} Error.
 */
export function getEntityRecordsError( state, name, query = {} ) {
	const queriedState = get( state.entities.data, [
		name,
		'queriedData',
		'errors',
	] );
	return getQueriedError( queriedState, query );
}

// /**
//  * Invokes the selector for whether a given name in a given registered
//  * reducer store is in the midst of resolving.
//  *
//  * @param {Object} state
//  * @param {string} name
//  * @param {*[]} args
//  * @return {boolean}  Whether resolution is in progress.
//  */
// export const isResolving = ( state, name, ...args ) => {
// 	return (
// 		select( 'core/data' ).isResolving( STORE_NAME, name, ...args ) === true
// 	);
// };
//
// /**
//  * Helper indicating whether the given resourceName, name, and queryString
//  * is being resolved or not.
//  *
//  * @param {Object} state
//  * @param {string} name
//  * @param {Object} args
//  * @return {boolean} Returns true if the selector is currently requesting items.
//  */
// export function isRequesting( state, name, ...args ) {
// 	return isResolving( name, args );
// }

// /**
//  * Check if collection is requesting.
//  *
//  * @param {Object} state
//  * @param {string} name
//  * @param {Object}  query
//  * @return {boolean} If requesting.
//  */
// export function isRequestingEntityRecords( state, name, query = {} ) {
// 	// console.log( isResolving( 'getEntityRecords', name, query ) );
// 	return false; //isRequesting( state, 'getEntityRecords', name, query );
// }

// /**
//  * Check if single item is requesting.
//  *
//  * @param {Object} state
//  * @param {string} name
//  * @param {number}  id
//  * @param {Object}  query
//  * @return {boolean} If requesting.
//  */
// export function isRequestingEntityRecord(
// 	state,
// 	name,
// 	id = undefined,
// 	query = {}
// ) {
// 	return false; //isRequesting( state, 'getEntityRecord', name, query, id );
// }
