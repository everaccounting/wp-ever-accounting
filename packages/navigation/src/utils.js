/**
 * Internal dependencies
 */
import { getHistory } from './history';
/**
 * External dependencies
 */
import { omit, identity, isArray, has, isEqual, pickBy } from 'lodash';
import { parse } from 'qs';

/**
 * Get the current path from history.
 *
 * @return {string}  Current path.
 */
export const getPath = () => getHistory().location.pathname;

/**
 * Set path.
 *
 * @param {string} path Path to set.
 */
export function setPath( path ) {
	// if path contains slash at the beginning, then absolute path is used otherwise relative path is used.
	const isAbsolutePath = path.startsWith( '/' );
	// remove slash from the end of the path.
	const currentPath = getPath().replace( /\/$/, '' );
	getHistory().navigate( {
		pathname: isAbsolutePath ? path : `${ currentPath }/${ path }`,
	} );
}

/**
 * Get the current query string, parsed into an object, from history.
 *
 * @return {Object}  Current query object, defaults to empty object.
 */
export function getQuery() {
	const search = getHistory().location.search;
	if ( search.length ) {
		return omit( parse( search.substring( 1 ) ) || {}, identity );
	}
	return {};
}

/**
 * Set query.
 *
 * @param {Object} query        object of params to be updated.
 * @param {string} path         Relative path (defaults to current path).
 * @param {Object} currentQuery object of current query params (defaults to current querystring).
 */
export function setQuery( query, path = getPath(), currentQuery = getQuery() ) {
	const queryArgs = pickBy( { ...currentQuery, ...query }, identity );
	getHistory().navigate( {
		pathname: path,
		search: `?${ new URLSearchParams( queryArgs ).toString() }`,
	} );
}

/**
 * Remove query ags
 *
 * @param {string | Array} key
 * @param {Object}         query
 */
export function removeQuery( key, query = getQuery() ) {
	const queryArgs = omit( query, Array.isArray( key ) ? key : [ key ] );
	getHistory().navigate( {
		pathname: getPath(),
		search: `?${ new URLSearchParams( queryArgs ).toString() }`,
	} );
}

export function navigate( query, path = getPath(), currentQuery = getQuery() ) {
	const queryArgs = pickBy( { ...currentQuery, ...query }, identity );
	getHistory().navigate( {
		pathname: path || getPath(),
		search: `?${ new URLSearchParams( queryArgs ).toString() }`,
	} );
}

/**
 * Get table query.
 *
 * @param {Array|Object} whitelists Extra params.
 * @param {Object}       defaults   Extra params.
 * @param {Function}     filter     Extra params.
 * @param {Object}       query      Extra params.
 * @return {{}} query.
 */
export function getTableQuery(
	whitelists = {},
	defaults = {},
	filter = ( x ) => x,
	query = getQuery()
) {
	if ( isArray( whitelists ) ) {
		whitelists = whitelists.reduce( ( acc, whitelist ) => {
			// eslint-disable-next-line no-unused-vars
			return { ...acc, [ whitelist ]: ( x, query ) => x };
		}, {} );
	}

	defaults = {
		...defaults,
		orderby: 'id',
		order: 'desc',
		per_page: 20,
		paged: 1,
	};

	whitelists = {
		...whitelists,
		search: ( search, query ) => query.search || '',
		paged: ( paged, query ) => parseInt( query.paged, 10 ) || 1,
		orderby: ( orderby, query ) => query.orderby || defaults.orderby,
		order: ( order, query ) => ( query.order === 'asc' ? 'asc' : defaults.order ),
	};
	query = Object.keys( query ).reduce( ( acc, queryKey ) => {
		if ( has( whitelists, [ queryKey ] ) ) {
			const queryValue = whitelists[ queryKey ]( query[ queryKey ], query );
			if ( has( defaults, [ queryKey ] ) && isEqual( defaults[ queryKey ], queryValue ) ) {
				return acc;
			}
			acc = {
				...acc,
				[ queryKey ]: queryValue,
			};
		}

		return acc;
	}, {} );
	return filter( pickBy( query, identity ) );
}
