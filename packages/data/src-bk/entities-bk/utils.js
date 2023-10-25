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
 * details (`page` and `perPage`, or default values). All other properties are
 * encoded into a stable (idempotent) `stableKey` value.
 *
 * @param {Object} query Optional query object.
 *
 * @return {Object} Query parts.
 */
export const getQueryParts = withWeakMapCache( ( query ) => {
	const parts = {
		page: 1,
		perPage: 10,
		fields: null,
		include: null,
		context: 'default',
	};

	// Ensure stable key by sorting keys. Also more efficient for iterating.
	const keys = Object.keys( query ).sort();

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
		}
	}

	return parts;
} );

export const getStableQueryKey = ( query = {} ) => {
	// Remove all the null, empty string, undefined, false, 0 values.
	const filteredQuery = Object.keys( query )
		.filter( ( key ) => {
			const value = query[ key ];
			return value !== null && value !== '' && value !== undefined && value !== false && value !== 0;
		} )
		.sort();

	// Join the remaining keys with '&'.
	return filteredQuery.join( '&' );
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
