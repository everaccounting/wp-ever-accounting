/**
 * External dependencies
 */
import { pickBy } from 'lodash';
import fastDeepEqual from 'fast-deep-equal/es6';

/**
 * WordPress dependencies
 */
import { addQueryArgs } from '@wordpress/url';

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
export const getQueryParts = withWeakMapCache( ( query ) => {
	const parts = {
		stableKey: '',
		page: 1,
		perPage: 20,
		fields: null,
		include: null,
		context: 'default',
	};
	const keys = Object.keys( pickBy( query ) ).sort();
	for ( let i = 0; i < keys.length; i++ ) {
		const key = keys[ i ];
		let value = query[ key ];
		switch ( key ) {
			case 'paged':
			case 'page':
				parts[ key ] = Number( value );
				break;
			case 'per_page':
			case 'perPage':
				parts.perPage = Number( value );
				break;
			case 'context':
				parts.context = value;
				break;
			default:
				// While in theory, we could exclude "_fields" from the stableKey
				// because two request with different fields have the same results
				// We're not able to ensure that because the server can decide to omit
				// fields from the response even if we explicitly asked for it.
				// Example: Asking for titles in posts without title support.
				if ( key === '_fields' ) {
					parts.fields = getNormalizedCommaSeparable( value ) ?? [];
					// Make sure to normalize value for `stableKey`
					value = parts.fields.join();
				}

				// Two requests with different include values cannot have same results.
				if ( key === 'include' ) {
					if ( typeof value === 'number' ) {
						value = value.toString();
					}
					parts.include = ( getNormalizedCommaSeparable( value ) ?? [] ).map( Number );
					// Normalize value for `stableKey`.
					value = parts.include.join();
				}

				// While it could be any deterministic string, for simplicity's
				// sake mimic querystring encoding for stable key.
				//
				// TODO: For consistency with PHP implementation, addQueryArgs
				// should accept a key value pair, which may optimize its
				// implementation for our use here, vs. iterating an object
				// with only a single key.
				parts.stableKey += ( parts.stableKey ? '&' : '' ) + addQueryArgs( '', { [ key ]: value } ).slice( 1 );
		}
	}

	return parts;
} );

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
 * Checks whether the attribute is a "raw" attribute or not.
 *
 * @param {Object} entity    Entity record.
 * @param {string} attribute Attribute name.
 *
 * @return {boolean} Is the attribute raw
 */
export function isRawAttribute( entity, attribute ) {
	return ( entity.rawAttributes || [] ).includes( attribute );
}
