/**
 * External dependencies
 */
import createSelector from 'rememo';
import { set, map, find, get, filter, compact, defaultTo } from 'lodash';

/**
 * WordPress dependencies
 */
import { createRegistrySelector } from '@wordpress/data';
import deprecated from '@wordpress/deprecated';
import { addQueryArgs } from '@wordpress/url';

/**
 * Internal dependencies
 */
import { STORE_NAME } from './name';
import { getQueriedItems } from './queried-data';
import { DEFAULT_ENTITY_KEY } from './entities';
import { getNormalizedCommaSeparable } from './utils';

/**
 * Shared reference to an empty array for cases where it is important to avoid
 * returning a new array reference on every invocation, as in a connected or
 * other pure component which performs `shouldComponentUpdate` check on props.
 * This should be used as a last resort, since the normalized data should be
 * maintained by the reducer result in state.
 *
 * @type {Array}
 */
const EMPTY_ARRAY = [];

/**
 * Returns whether the entities for the give kind are loaded.
 *
 * @param {Object} state   Data state.
 * @param {string} kind  Entity kind.
 *
 * @return {boolean} Whether the entities are loaded
 */
export function getEntitiesByKind( state, kind ) {
	return filter( state.entities.config, { kind } );
}

/**
 * Returns the entity object given its kind and name.
 *
 * @param {Object} state   Data state.
 * @param {string} kind  Entity kind.
 * @param {string} name  Entity name.
 *
 * @return {Object} Entity
 */
export function getEntity( state, kind, name ) {
	return find( state.entities.config, { kind, name } );
}

/**
 * Returns the Entity's record object by key. Returns `null` if the value is not
 * yet received, undefined if the value entity is known to not exist, or the
 * entity object if it exists and is received.
 *
 * @param {Object}  state State tree
 * @param {string}  kind  Entity kind.
 * @param {string}  name  Entity name.
 * @param {number}  key   Record's key
 * @param {?Object} query Optional query.
 *
 * @return {Object?} Record.
 */
export function getEntityRecord( state, kind, name, key, query ) {
	const queriedState = get( state.entities.data, [
		kind,
		name,
		'queriedData',
	] );
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
 * Returns the Entity's record object by key. Doesn't trigger a resolver nor requests the entity from the API if the entity record isn't available in the local state.
 *
 * @param {Object} state  State tree
 * @param {string} kind   Entity kind.
 * @param {string} name   Entity name.
 * @param {number} key    Record's key
 *
 * @return {Object|null} Record.
 */
export function __experimentalGetEntityRecordNoResolver(
	state,
	kind,
	name,
	key
) {
	return getEntityRecord( state, kind, name, key );
}

/**
 * Returns the entity's record object by key,
 * with its attributes mapped to their raw values.
 *
 * @param {Object} state  State tree.
 * @param {string} kind   Entity kind.
 * @param {string} name   Entity name.
 * @param {number} key    Record's key.
 *
 * @return {Object?} Object with the entity's raw attributes.
 */
export const getRawEntityRecord = createSelector(
	( state, kind, name, key ) => {
		const record = getEntityRecord( state, kind, name, key );
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
	( state ) => [ state.entities.data ]
);

/**
 * Returns true if records have been received for the given set of parameters,
 * or false otherwise.
 *
 * @param {Object}  state State tree
 * @param {string}  kind  Entity kind.
 * @param {string}  name  Entity name.
 * @param {?Object} query Optional terms query.
 *
 * @return {boolean} Whether entity records have been received.
 */
export function hasEntityRecords( state, kind, name, query ) {
	return Array.isArray( getEntityRecords( state, kind, name, query ) );
}

/**
 * Returns the Entity's records.
 *
 * @param {Object}  state State tree
 * @param {string}  kind  Entity kind.
 * @param {string}  name  Entity name.
 * @param {?Object} query Optional terms query.
 *
 * @return {?Array} Records.
 */
export function getEntityRecords( state, kind, name, query ) {
	// Queried data state is prepopulated for all known entities. If this is not
	// assigned for the given parameters, then it is known to not exist. Thus, a
	// return value of an empty array is used instead of `null` (where `null` is
	// otherwise used to represent an unknown state).
	const queriedState = get( state.entities.data, [
		kind,
		name,
		'queriedData',
	] );
	if ( ! queriedState ) {
		return EMPTY_ARRAY;
	}
	return getQueriedItems( queriedState, query );
}

/**
 * Returns the  list of dirty entity records.
 *
 * @param {Object} state State tree.
 *
 * @return {[{ title: string, key: string, name: string, kind: string }]} The list of updated records
 */
export const __experimentalGetDirtyEntityRecords = createSelector(
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
 * @param {string} kind     Entity kind.
 * @param {string} name     Entity name.
 * @param {number} recordId Record ID.
 *
 * @return {Object?} The entity record's edits.
 */
export function getEntityRecordEdits( state, kind, name, recordId ) {
	return get( state.entities.data, [ kind, name, 'edits', recordId ] );
}

/**
 * Returns the specified entity record's non transient edits.
 *
 * Transient edits don't create an undo level, and
 * are not considered for change detection.
 * They are defined in the entity's config.
 *
 * @param {Object} state    State tree.
 * @param {string} kind     Entity kind.
 * @param {string} name     Entity name.
 * @param {number} recordId Record ID.
 *
 * @return {Object?} The entity record's non transient edits.
 */
export const getEntityRecordNonTransientEdits = createSelector(
	( state, kind, name, recordId ) => {
		const { transientEdits } = getEntity( state, kind, name ) || {};
		const edits = getEntityRecordEdits( state, kind, name, recordId ) || {};
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
 * @param {string} kind     Entity kind.
 * @param {string} name     Entity name.
 * @param {number} recordId Record ID.
 *
 * @return {boolean} Whether the entity record has edits or not.
 */
export function hasEditsForEntityRecord( state, kind, name, recordId ) {
	return (
		isSavingEntityRecord( state, kind, name, recordId ) ||
		Object.keys(
			getEntityRecordNonTransientEdits( state, kind, name, recordId )
		).length > 0
	);
}

/**
 * Returns the specified entity record, merged with its edits.
 *
 * @param {Object} state    State tree.
 * @param {string} kind     Entity kind.
 * @param {string} name     Entity name.
 * @param {number} recordId Record ID.
 *
 * @return {Object?} The entity record, merged with its edits.
 */
export const getEditedEntityRecord = createSelector(
	( state, kind, name, recordId ) => ( {
		...getRawEntityRecord( state, kind, name, recordId ),
		...getEntityRecordEdits( state, kind, name, recordId ),
	} ),
	( state ) => [ state.entities.data ]
);

/**
 * Returns true if the specified entity record is autosaving, and false otherwise.
 *
 * @param {Object} state    State tree.
 * @param {string} kind     Entity kind.
 * @param {string} name     Entity name.
 * @param {number} recordId Record ID.
 *
 * @return {boolean} Whether the entity record is autosaving or not.
 */
export function isAutosavingEntityRecord( state, kind, name, recordId ) {
	const { pending, isAutosave } = get(
		state.entities.data,
		[ kind, name, 'saving', recordId ],
		{}
	);
	return Boolean( pending && isAutosave );
}

/**
 * Returns true if the specified entity record is saving, and false otherwise.
 *
 * @param {Object} state    State tree.
 * @param {string} kind     Entity kind.
 * @param {string} name     Entity name.
 * @param {number} recordId Record ID.
 *
 * @return {boolean} Whether the entity record is saving or not.
 */
export function isSavingEntityRecord( state, kind, name, recordId ) {
	return get(
		state.entities.data,
		[ kind, name, 'saving', recordId, 'pending' ],
		false
	);
}

/**
 * Returns true if the specified entity record is deleting, and false otherwise.
 *
 * @param {Object} state    State tree.
 * @param {string} kind     Entity kind.
 * @param {string} name     Entity name.
 * @param {number} recordId Record ID.
 *
 * @return {boolean} Whether the entity record is deleting or not.
 */
export function isDeletingEntityRecord( state, kind, name, recordId ) {
	return get(
		state.entities.data,
		[ kind, name, 'deleting', recordId, 'pending' ],
		false
	);
}

/**
 * Returns the specified entity record's last save error.
 *
 * @param {Object} state    State tree.
 * @param {string} kind     Entity kind.
 * @param {string} name     Entity name.
 * @param {number} recordId Record ID.
 *
 * @return {Object?} The entity record's save error.
 */
export function getLastEntitySaveError( state, kind, name, recordId ) {
	return get( state.entities.data, [
		kind,
		name,
		'saving',
		recordId,
		'error',
	] );
}

/**
 * Returns the specified entity record's last delete error.
 *
 * @param {Object} state    State tree.
 * @param {string} kind     Entity kind.
 * @param {string} name     Entity name.
 * @param {number} recordId Record ID.
 *
 * @return {Object?} The entity record's save error.
 */
export function getLastEntityDeleteError( state, kind, name, recordId ) {
	return get( state.entities.data, [
		kind,
		name,
		'deleting',
		recordId,
		'error',
	] );
}
