/**
 * External dependencies
 */
import { pickBy } from 'lodash';
import fastDeepEqual from 'fast-deep-equal/es6';

/**
 * A higher-order reducer creator which invokes the original reducer only if
 * the dispatching action matches the given predicate, **OR** if state is
 * initializing (undefined).
 *
 * @param {Function} isMatch Function predicate for allowing reducer call.
 *
 * @return {Function} Higher-order reducer.
 */
export const ifMatchingAction = ( isMatch ) => ( reducer ) => ( state, action ) => {
	if ( state === undefined || isMatch( action ) ) {
		return reducer( state, action );
	}

	return state;
};

/**
 * Higher-order reducer creator which substitutes the action object before
 * passing to the original reducer.
 *
 * @param {Function} replacer Function mapping original action to replacement.
 *
 * @return {Function} Higher-order reducer.
 */
export const replaceAction = ( replacer ) => ( reducer ) => ( state, action ) => {
	return reducer( state, replacer( action ) );
};

/**
 * Given a value which can be specified as one or the other of a comma-separated
 * string or an array, returns a value normalized to an array of strings, or
 * null if the value cannot be interpreted as either.
 *
 * @param {string|string[]|*} value
 *
 * @return {?(string[])} Normalized field value.
 */
export const getNormalizedCommaSeparable = ( value ) => {
	if ( typeof value === 'string' ) {
		return value.split( ',' );
	} else if ( Array.isArray( value ) ) {
		return value;
	}

	return null;
};

/**
 * Given a function, returns an enhanced function which caches the result and
 * tracks in WeakMap. The result is only cached if the original function is
 * passed a valid object-like argument (requirement for WeakMap key).
 *
 * @param {Function} fn Original function.
 *
 * @return {Function} Enhanced caching function.
 */
function withWeakMapCache( fn ) {
	const cache = new WeakMap();

	return ( key ) => {
		let value;
		if ( cache.has( key ) ) {
			value = cache.get( key );
		} else {
			value = fn( key );

			// Can reach here if key is not valid for WeakMap, since `has`
			// will return false for invalid key. Since `set` will throw,
			// ensure that key is valid before setting into cache.
			if ( key !== null && typeof key === 'object' ) {
				cache.set( key, value );
			}
		}

		return value;
	};
}

/**
 * Given a query object, returns an object of parts, including pagination
 * details (`page` and `per_page`, or default values). All other properties are
 * encoded into a stable (idempotent) `stableKey` value.
 *
 * @param {Object} query Optional query object.
 *
 * @return {Object} Query parts.
 */
export const getQueryParts = withWeakMapCache( ( query = {} ) => {
	const parts = {
		stableKey: '',
	};
	const keys = Object.keys( pickBy( query ) ).sort();
	for ( let i = 0; i < keys.length; i++ ) {
		const key = keys[ i ];
		let value = query[ key ];
		switch ( key ) {
			case 'page':
				parts[ key ] = Number( value );
				break;
			case 'per_page':
				parts.perPage = Number( value );
				break;
			case 'include':
				parts.include = getNormalizedCommaSeparable( value ).map( Number );
				break;
			default:
				if ( key === '_fields' ) {
					parts.fields = getNormalizedCommaSeparable( value );
					// Make sure to normalize value for `stableKey`
					value = parts.fields.join();
				}
				// ensure every segment added to stableKey joins with `&`.
				parts.stableKey += parts.stableKey ? `&${ key }=${ value }` : `${ key }=${ value }`;
		}
	}

	return parts;
} );

/**
 * Sets the value at path of object.
 * If a portion of path doesn’t exist, it’s created.
 * Arrays are created for missing index properties while objects are created
 * for all other missing properties.
 *
 * This function intentionally mutates the input object.
 *
 * Inspired by _.set().
 *
 * @see https://lodash.com/docs/4.17.15#set
 *
 * @param {Object} object Object to modify
 * @param {Array}  path   Path of the property to set.
 * @param {*}      value  Value to set.
 */
export const setNestedValue = ( object, path, value ) => {
	if ( ! object || typeof object !== 'object' ) {
		return object;
	}

	path.reduce( ( acc, key, idx ) => {
		if ( acc[ key ] === undefined ) {
			if ( Number.isInteger( path[ idx + 1 ] ) ) {
				acc[ key ] = [];
			} else {
				acc[ key ] = {};
			}
		}
		if ( idx === path.length - 1 ) {
			acc[ key ] = value;
		}
		return acc[ key ];
	}, object );

	return object;
};

/**
 * Given the current and next item entity record, returns the minimally "modified"
 * result of the next item, preferring value references from the original item
 * if equal. If all values match, the original item is returned.
 *
 * @param {Object} item     Original item.
 * @param {Object} nextItem Next item.
 *
 * @return {Object} Minimally modified merged item.
 */
export const conservativeMapItem = ( item, nextItem ) => {
	// Return next item in its entirety if there is no original item.
	if ( ! item ) {
		return nextItem;
	}

	let hasChanges = false;
	const result = {};
	for ( const key in nextItem ) {
		if ( fastDeepEqual( item[ key ], nextItem[ key ] ) ) {
			result[ key ] = item[ key ];
		} else {
			hasChanges = true;
			result[ key ] = nextItem[ key ];
		}
	}

	if ( ! hasChanges ) {
		return item;
	}

	// Only at this point, backfill properties from the original item which
	// weren't explicitly set into the result above. This is an optimization
	// to allow `hasChanges` to return early.
	for ( const key in item ) {
		if ( ! result.hasOwnProperty( key ) ) {
			result[ key ] = item[ key ];
		}
	}

	return result;
};

/**
 * Helper function to filter out entities with certain IDs.
 * Entities are keyed by their ID.
 *
 * @param {Object} records Entity objects, keyed by entity ID.
 * @param {Array}  ids     Entity IDs to filter out.
 *
 * @return {Object} Filtered records.
 */
export const removeRecordsById = ( records, ids ) => {
	return Object.fromEntries(
		Object.entries( records ).filter(
			( [ id ] ) =>
				! ids.some( ( itemId ) => {
					if ( Number.isInteger( itemId ) ) {
						return itemId === +id;
					}
					return itemId === id;
				} )
		)
	);
};

/**
 * Returns a merged array of item IDs, given details of the received paginated
 * items. The array is sparse-like with `undefined` entries where holes exist.
 *
 * @param {?Array<number>} itemIds     Original item IDs (default empty array).
 * @param {number[]}       nextItemIds Item IDs to merge.
 * @param {number}         page        Page of items merged.
 * @param {number}         perPage     Number of items per page.
 *
 * @return {number[]} Merged array of item IDs.
 */
export const getMergedItemIds = ( itemIds, nextItemIds, page, perPage ) => {
	const receivedAllIds = page === 1 && perPage === -1;
	if ( receivedAllIds ) {
		return nextItemIds;
	}
	const nextItemIdsStartIndex = ( page - 1 ) * perPage;
	// If later page has already been received, default to the larger known
	// size of the existing array, else calculate as extending the existing.
	const size = Math.max( itemIds?.length ?? 0, nextItemIdsStartIndex + nextItemIds.length );

	// Preallocate array since size is known.
	const mergedItemIds = new Array( size );

	for ( let i = 0; i < size; i++ ) {
		// Preserve existing item ID except for subset of range of next items.
		const isInNextItemsRange = i >= nextItemIdsStartIndex && i < nextItemIdsStartIndex + nextItemIds.length;

		mergedItemIds[ i ] = isInNextItemsRange ? nextItemIds[ i - nextItemIdsStartIndex ] : itemIds?.[ i ];
	}

	return mergedItemIds;
};

/**
 * Higher-order reducer creator which creates a combined reducer object, keyed
 * by a property on the action object.
 *
 * @param {string} actionProperty Action property by which to key object.
 *
 * @return {Function} Higher-order reducer.
 */
export const onSubKey =
	( actionProperty ) =>
		( reducer ) =>
			( state = {}, action ) => {
				// Retrieve subkey from action. Do not track if undefined; useful for cases
				// where reducer is scoped by action shape.
				const key = action[ actionProperty ];
				if ( key === undefined ) {
					return state;
				}

				// Avoid updating state if unchanged. Note that this also accounts for a
				// reducer which returns undefined on a key which is not yet tracked.
				const nextKeyState = reducer( state[ key ], action );
				if ( nextKeyState === state[ key ] ) {
					return state;
				}

				return {
					...state,
					[ key ]: nextKeyState,
				};
			};
