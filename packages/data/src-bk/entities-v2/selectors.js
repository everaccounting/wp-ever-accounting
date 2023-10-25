/**
 * External dependencies
 */
import { get, set } from 'lodash';
import createSelector from 'rememo';
import EquivalentKeyMap from 'equivalent-key-map';

/**
 * Internal dependencies
 */
import { getQueryParts, getNormalizedCommaSeparable, setNestedValue } from './utils';
const queriedItemsCacheByState = new WeakMap();

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
 * Returns the Entity's record object by key. Returns `null` if the value is not
 * yet received, undefined if the value entity is known to not exist, or the
 * entity object if it exists and is received.
 *
 * @param state State tree
 * @param name  Entity name.
 * @param key   Record's key
 * @param query Optional query. If requesting specific
 *              fields, fields must always include the ID. For valid query parameters see the [Reference](https://developer.wordpress.org/rest-api/reference/) in the REST API Handbook and select the entity kind. Then see the arguments available "Retrieve a [Entity kind]".
 *
 * @return Record.
 */
export const getRecord = createSelector(
	( state, name, key, query ) => {
		if ( ! state.records?.[ name ]?.items ) {
			return undefined;
		}
		const context = query?.context ?? 'default';

		if ( query === undefined ) {
			// If expecting a complete item, validate that completeness.
			if ( ! state.records?.[ name ]?.requesting[ context ]?.[ key ] ) {
				return undefined;
			}

			return state.records?.[ name ]?.items[ context ]?.[ key ];
		}

		const item = state.records?.[ name ]?.items[ context ]?.[ key ];
		if ( item && query._fields ) {
			const filteredItem = {};
			const fields = getNormalizedCommaSeparable( query._fields ) ?? [];
			for ( let f = 0; f < fields.length; f++ ) {
				const field = fields[ f ].split( '.' );
				let value = item;
				field.forEach( ( fieldName ) => {
					value = value[ fieldName ];
				} );
				setNestedValue( filteredItem, field, value );
			}
			return filteredItem;
		}

		return item;
	},
	( state, name, recordId, query ) => {
		const context = query?.context ?? 'default';
		return [
			state.entities.records?.[ name ]?.queriedData?.items[ context ]?.[ recordId ],
			state.entities.records?.[ name ]?.queriedData?.itemIsComplete[ context ]?.[ recordId ],
		];
	}
);

/**
 * Returns items for a given query, or null if the items are not known.
 *
 * @param {Object}  state State object.
 * @param {?Object} query Optional query.
 *
 * @return {?Array} Query items.
 */
function getQueriedItemsUncached( state, query ) {
	const { stableKey, page, per_page, include, fields } = getQueryParts( query );
	let itemIds;
	if ( Array.isArray( include ) && ! stableKey ) {
		// If the parsed query yields a set of IDs, but otherwise no filtering,
		// it's safe to consider targeted item IDs as the include set. This
		// doesn't guarantee that those objects have been queried, which is
		// accounted for below in the loop `null` return.
		itemIds = include;
		// TODO: Avoid storing the empty stable string in reducer, since it
		// can be computed dynamically here always.
	} else if ( state.queries[ stableKey ] ) {
		itemIds = state.queries[ stableKey ];
	}

	if ( ! itemIds ) {
		return null;
	}
	const startOffset = per_page === -1 ? 0 : ( page - 1 ) * per_page;
	const endOffset = per_page === -1 ? itemIds.length : Math.min( startOffset + per_page, itemIds.length );

	const items = [];
	for ( let i = startOffset; i < endOffset; i++ ) {
		const itemId = itemIds[ i ];
		if ( Array.isArray( include ) && ! include.includes( itemId ) ) {
			continue;
		}

		if ( ! state.items.hasOwnProperty( itemId ) ) {
			return null;
		}

		const item = state.items[ itemId ];

		let filteredItem;
		if ( Array.isArray( fields ) ) {
			filteredItem = {};

			for ( let f = 0; f < fields.length; f++ ) {
				const field = fields[ f ].split( '.' );
				const value = get( item, field );
				set( filteredItem, field, value );
			}
		} else {
			// If expecting a complete item, validate that completeness, or
			// otherwise abort.
			if ( ! state.itemIsComplete[ itemId ] ) {
				return null;
			}

			filteredItem = item;
		}

		items.push( filteredItem );
	}

	return items;
}

/**
 * Returns items for a given query, or null if the items are not known. Caches
 * result both per state (by reference) and per query (by deep equality).
 * The caching approach is intended to be durable to query objects which are
 * deeply but not referentially equal, since otherwise:
 *
 * `getQueriedItems( state, {} ) !== getQueriedItems( state, {} )`
 *
 * @param {Object}  state State object.
 * @param {?Object} query Optional query.
 *
 * @return {?Array} Query items.
 */
export const getQueriedItems = createSelector( ( state, query = {} ) => {
	let queriedItemsCache = queriedItemsCacheByState.get( state );
	if ( queriedItemsCache ) {
		const queriedItems = queriedItemsCache.get( query );
		if ( queriedItems !== undefined ) {
			return queriedItems;
		}
	} else {
		queriedItemsCache = new EquivalentKeyMap();
		queriedItemsCacheByState.set( state, queriedItemsCache );
	}

	const items = getQueriedItemsUncached( state, query );
	queriedItemsCache.set( query, items );
	return items;
} );

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
export const getRecords = ( state, name, query ) => {
	const queriedState = state.records?.[ name ]?.items;
	if ( ! queriedState ) {
		return null;
	}

	return state.records?.[ name ]?.items;
};

/**
 * Returns the records count for the given set of parameters.
 *
 * @param {Object}  state    State tree
 * @param {string}  name     Entity name.
 * @param {?Object} query    Optional terms query.
 *
 * @param {Array}   defaults Default value.
 * @return {Array} Record Count.
 */
export const getRecordsCount = ( state, name, query, defaults = 0 ) => {
	const queriedState = state.records?.[ name ]?.counts;
	if ( ! queriedState ) {
		return defaults;
	}
	const { page, per_page, include, _fields, ...totalsQuery } = query || {};
	const { stableKey } = getQueryParts( totalsQuery );
	if ( state.records?.[ name ]?.counts[ stableKey ] ) {
		return state.records?.[ name ]?.counts[ stableKey ];
	}
	return defaults;
};

/**
 * Returns true if records have been received for the given set of parameters,
 * or false otherwise.
 *
 * @param {Object}  state State tree
 * @param {string}  name  Entity name.
 * @param {?Object} query Optional terms query.
 *
 * @return  {boolean} Whether records have been received.
 */
export const hasRecords = ( state, name, query ) => {
	return Array.isArray( getRecords( state, name, query ) );
};

/**
 * Returns the specified entity record's edits.
 *
 * @param {Object} state    State tree
 * @param {string} name     Entity name.
 * @param {number} recordId Record ID.
 *
 * @return {Object} The entity record's edits.
 */
export function getRecordEdits( state, name, recordId ) {
	return state.records?.[ name ]?.edits?.[ recordId ];
}

/**
 * Returns the specified entity record's non-transient edits.
 *
 * Transient edits don't create an undo level, and
 * are not considered for change detection.
 * They are defined in the entity's config.
 *
 * @param {Object} state    State tree
 * @param {string} name     Entity name.
 * @param {number} recordId Record ID.
 *
 * @return The entity record's non-transient edits.
 */
export const getRecordNonTransientEdits = createSelector(
	( state, name, recordId ) => {
		const { transientEdits } = getEntity( state, name ) || {};
		const edits = getRecordEdits( state, name, recordId ) || {};
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
	( state, name, recordId ) => [ state.entities, state.records?.[ name ]?.edits?.[ recordId ] ]
);

/**
 * Returns true if the specified entity record has edits,
 * and false otherwise.
 *
 * @param {Object} state    State tree
 * @param {string} name     Entity name.
 * @param {number} recordId Record ID.
 *
 * @return {boolean} Whether the specified entity record has edits.
 */
export const hasEditsForRecord = ( state, name, recordId ) => {
	return (
		isSavingRecord( state, name, recordId ) ||
		Object.keys( getRecordNonTransientEdits( state, name, recordId ) ).length > 0
	);
};

/**
 * Returns the specified entity record, merged with its edits.
 *
 * @param {Object} state    State tree
 * @param {string} name     Entity name.
 * @param {number} recordId Record ID.
 *
 * @return The entity record, merged with its edits.
 */
export const getEditedRecord = createSelector(
	( state, name, recordId ) => ( {
		...getRecord( state, name, recordId ),
		...getRecordEdits( state, name, recordId ),
	} ),
	( state, name, recordId, query ) => {
		const context = query?.context ?? 'default';
		return [
			state.entities,
			state.records?.[ name ]?.items[ context ]?.[ recordId ],
			state.records?.[ name ]?.requesting[ context ]?.[ recordId ],
			state.records?.[ name ]?.edits?.[ recordId ],
		];
	}
);

/**
 * Returns true if the specified entity record is saving, and false otherwise.
 *
 * @param {Object} state    State tree
 * @param {string} name     Entity name.
 * @param {number} recordId Record ID.
 *
 * @return {boolean} Whether the entity record is saving or not.
 */
export const isSavingRecord = ( state, name, recordId ) => {
	return state.records?.[ name ]?.saving?.[ recordId ]?.pending ?? false;
};

/**
 * Returns true if the specified entity record is deleting, and false otherwise.
 *
 * @param {Object} state    State tree
 * @param {string} name     Entity name.
 * @param {number} recordId Record ID.
 *
 * @return {boolean} Whether the entity record is deleting or not.
 */
export const isDeletingRecord = ( state, name, recordId ) => {
	return state.records?.[ name ]?.deleting?.[ recordId ]?.pending ?? false;
};
