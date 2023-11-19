/**
 * External dependencies
 */
import { get, set, isEmpty } from 'lodash';
import createSelector from 'rememo';

/**
 * Internal dependencies
 */
import { EMPTY_ARRAY } from './constants';
import { getQueryParts, getNormalizedCommaSeparable, isRawAttribute } from './utils';

/**
 * Returns the entity config given its kind and name.
 *
 * @param {Object} state Data state.
 * @param {string} name  Entity name.
 *
 * @return {Object} Entity config.
 */
export const getEntity = ( state, name ) => {
	return get( state, [ 'entities' ], [] ).find( ( entity ) => entity.name === name );
};

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
export const getRecord = createSelector(
	( state, name, key, query = {} ) => {
		const { context } = getQueryParts( query );
		if ( ! state?.records[ name ]?.items || ! state?.records[ name ]?.items[ context ] ) {
			return undefined;
		}
		if ( query === undefined ) {
			if ( ! state.records[ name ].fetching[ key ] ) {
				return undefined;
			}

			return state.records[ name ].items[ context ][ key ];
		}
		const item = state.records[ name ].items[ context ][ key ];
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
	},
	( state, name, key, query ) => {
		const context = query?.context ?? 'default';
		return [
			state.records?.[ name ]?.items[ context ]?.[ key ],
			state.records?.[ name ]?.fetching[ context ]?.[ key ],
		];
	}
);

/**
 * Returns the entity's record object by key,
 * with its attributes mapped to their raw values.
 *
 * @param state State tree.
 * @param name  Entity name.
 * @param key   Record's key.
 *
 * @return Object with the entity's raw attributes.
 */
export const getRawRecord = createSelector(
	( state, name, key ) => {
		const record = getRecord( state, name, key );
		return (
			record &&
			Object.keys( record ).reduce( ( accumulator, _key ) => {
				if ( isRawAttribute( getEntity( state, name ), _key ) ) {
					// Because edits are the "raw" attribute values,
					// we return those from record selectors to make rendering,
					// comparisons, and joins with edits easier.
					accumulator[ _key ] = record[ _key ]?.raw ?? record[ _key ];
				} else {
					accumulator[ _key ] = record[ _key ];
				}
				return accumulator;
			}, {} )
		);
	},
	( state, name, key, query ) => {
		const context = query?.context ?? 'default';
		return [
			state.entities,
			state.records?.[ name ]?.items[ context ]?.[ key ],
			state.records?.[ name ]?.fetching[ context ]?.[ key ],
		];
	}
);

/**
 * Returns the Entity's records.
 *
 * @param {Object} state State tree
 * @param {string} name  Entity name.
 * @param {Object} query Optional terms query. If requesting specific
 *                       fields, fields must always include the ID. For valid query parameters see the [Reference](https://developer.wordpress.org/rest-api/reference/) in the REST API Handbook and select the entity kind. Then see the arguments available for "List [Entity kind]s".
 *
 * @return {Array} Records.
 */
export const getRecords = createSelector( ( state, name, query ) => {
	const { stableKey, context } = getQueryParts( query );
	const ids = state?.records?.[ name ]?.queries?.[ context ]?.[ stableKey ]?.data;
	if ( ! ids ) {
		return null;
	}
	return ids.map( ( id ) => state.records[ name ].items[ context ][ id ] ).filter( ( item ) => item !== undefined );
} );

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
	return Array.isArray( getRecords( state, name, query ) );
}

/**
 * Returns the Entity's records.
 *
 * @param {Object}  state    State tree
 * @param {string}  name     Entity name.
 * @param {?Object} query    Optional terms query.
 *
 * @param {Array}   defaults Default value.
 * @return {number} Record Count.
 */
export const getRecordsCount = createSelector( ( state, name, query = {} ) => {
	const { stableKey, context } = getQueryParts( query );
	return get( state, [ 'records', name, 'counts', context, stableKey, 'data' ], null );
} );

/**
 * Returns the specified entity record's edits.
 *
 * @param {Object}          state State tree.
 * @param {string}          name  Entity name.
 * @param {number | string} key   Entity record key.
 *
 * @return {Object} The entity record's edits.
 */
export function getRecordEdits( state, name, key ) {
	return get( state.records, [ name, 'edits', key ] );
}

/**
 * Returns the specified entity record's non transient edits.
 *
 * Transient edits don't create an undo level, and
 * are not considered for change detection.
 * They are defined in the entity's config.
 *
 * @param {Object}          state State tree.
 * @param {string}          name  Entity name.
 * @param {number | string} key   Entity record key.
 *
 * @return {Object} The entity record's non-transient edits.
 */
export const getRecordNonTransientEdits = createSelector(
	( state, name, key ) => {
		const { transientEdits } = getEntity( name ) || {};
		const edits = getRecordEdits( state, name, key ) || {};
		if ( ! transientEdits ) {
			return edits;
		}
		return Object.keys( edits ).reduce( ( acc, _key ) => {
			if ( ! transientEdits[ _key ] ) {
				acc[ _key ] = edits[ _key ];
			}
			return acc;
		}, {} );
	},
	( state ) => [ state.records, state.records.data ]
);

/**
 * Returns true if the specified entity record has edits,
 * and false otherwise.
 *
 * @param {Object}          state State tree.
 * @param {string}          name  Entity name.
 * @param {number | string} key   Entity record key.
 *
 * @return {boolean} Whether the entity record has edits or not.
 */
export function hasEditsForRecord( state, name, key ) {
	return (
		isSavingRecord( state, name, key ) || Object.keys( getRecordNonTransientEdits( state, name, key ) ).length > 0
	);
}

/**
 * Returns the specified entity record, merged with its edits.
 *
 * @param {Object}          state State tree.
 * @param {string}          name  Entity name.
 * @param {number | string} key   Entity record key.
 *
 * @return {Object} The entity record, merged with its edits.
 */
export const getEditedRecord = createSelector(
	( state, name, key ) => ( {
		...getRawRecord( state, name, key ),
		...getRecordEdits( state, name, key ),
	} ),
	( state ) => [ state.records.data ]
);
/**
 * Returns query error for the given entity name and query otherwise undefined.
 *
 * @param {Object}  state State object.
 * @param {string}  name  Entity name.
 * @param {?Object} query Optional query.
 *
 * @return {Object} Query error.
 */
export const getQueryError = createSelector( ( state, name, query = {} ) => {
	const { stableKey, context } = getQueryParts( query );
	return get( state, [ 'records', name, 'queries', context, stableKey, 'error' ], undefined );
} );

/**
 * Returns true if the specified entity record is saving, and false otherwise.
 *
 * @param {Object} state State tree.
 * @param {string} name  Entity name.
 *
 * @return {Function} Whether the entity record is saving or not.
 */
export function isSavingRecord( state, name ) {
	return ( key ) => {
		return get( state, [ 'records', name, 'saving', key, 'pending' ], false );
	};
}

/**
 * Returns true if the specified entity record is deleting, and false otherwise.
 *
 * @param {Object} state State tree.
 * @param {string} name  Entity name.
 *
 * @return {boolean} Whether the entity record is deleting or not.
 */
export function isDeletingRecord( state, name ) {
	return ( key ) => {
		return get( state, [ 'records', name, 'deleting', key, 'pending' ] ) === true;
	};
}

/**
 * Returns saving error for the given entity primary key and query otherwise undefined.
 *
 * @param {Object} state State object.
 * @param {string} name  Entity name.
 *
 * @return {Object} Query error.
 */
export const getSaveError = createSelector( ( state, name ) => {
	return ( recordId ) => {
		return get( state, [ 'records', name, 'saving', recordId, 'error' ], undefined );
	};
} );

/**
 * Returns deleting error for the given entity primary key and query otherwise undefined.
 *
 * @param {Object} state State object.
 * @param {string} name  Entity name.
 *
 * @return {Function} Query error.
 */
export const getDeleteError = createSelector( ( state, name ) => {
	return ( recordId ) => {
		return get( state, [ 'records', name, 'deleting', recordId, 'error' ], undefined );
	};
} );
