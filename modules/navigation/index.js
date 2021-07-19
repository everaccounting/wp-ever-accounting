/**
 * External dependencies
 */
/**
 * WordPress dependencies
 */
import { addQueryArgs } from '@wordpress/url';
import { parse } from 'qs';
import { identity, omit, pickBy, uniq } from 'lodash';

/**
 * Internal dependencies
 */
import { getHistory } from './history';
import * as navUtils from './index';
// For the above, import the module into itself. Functions consumed from this import can be mocked in tests.

// Expose history so all uses get the same history object.
export { getHistory };

/**
 * Get the current path from history.
 *
 * @return {string}  Current path.
 */
export const getPath = () => getHistory().location.pathname;

/**
 * Get the page from history.
 *
 * @return {string} Query String
 */
export const getPage = () => {
	const search = getHistory().location.search;
	if ( search.length ) {
		const query = parse( search.substring( 1 ) ) || {};
		const { page } = query;
		return page;
	}
	return null;
};

/**
 * Retrieve a string 'name' representing the current screen
 *
 * @param {Object} path Path to resolve, default to current
 * @return {string} Screen name
 */
export const getScreenFromPath = ( path = getPath() ) => {
	return path === '/'
		? 'homescreen'
		: path.replace( '/ea-react', '' ).replace( '/', '' );
};

/**
 * Get an array of IDs from a comma-separated query parameter.
 *
 * @param {string} queryString string value extracted from URL.
 * @return {Array} List of IDs converted to numbers.
 */
export function getIdsFromQuery( queryString = '' ) {
	return uniq(
		queryString
			.split( ',' )
			.map( ( id ) => parseInt( id, 10 ) )
			.filter( Boolean )
	);
}

/**
 * Get an ID from a query parameter.
 *
 * @return {Array} List of IDs converted to numbers.
 */
export function getIdFromQuery( query = getQuery() ) {
	const { id } = query;

	return parseInt( id, 10 );
}

/**
 * Get an array of searched words given a query.
 *
 * @param {Object} query Query object.
 * @return {Array} List of search words.
 */
export function getSearchWords( query = navUtils.getQuery() ) {
	if ( typeof query !== 'object' ) {
		throw new Error(
			'Invalid parameter passed to getSearchWords, it expects an object or no parameters.'
		);
	}
	const { search } = query;
	if ( ! search ) {
		return [];
	}
	if ( typeof search !== 'string' ) {
		throw new Error(
			"Invalid 'search' type. getSearchWords expects query's 'search' property to be a string."
		);
	}
	return search
		.split( ',' )
		.map( ( searchWord ) => searchWord.replace( '%2C', ',' ) );
}

/**
 * Return a URL with set query parameters.
 *
 * @param {Object} query object of params to be updated.
 * @param {string} path Relative path (defaults to current path).
 * @param {Object} currentQuery object of current query params (defaults to current querystring).
 * @return {string}  Updated URL merging query params into existing params.
 */
export function generatePath(
	query,
	path = getPath(),
	currentQuery = getQuery()
) {
	const page = getPage();
	const args = { page };
	if ( path !== '/' ) {
		args.path = path;
	}

	return addQueryArgs(
		'admin.php',
		pickBy( { ...args, ...currentQuery, ...query }, identity )
	);
}

/**
 * Get the current query string, parsed into an object, from history.
 *
 * @return {Object}  Current query object, defaults to empty object.
 */
export function getQuery() {
	const search = getHistory().location.search;
	if ( search.length ) {
		return omit( parse( search.substring( 1 ) ) || {}, [ 'page', 'path' ] );
	}
	return {};
}

/**
 * Get table query.
 *
 * @param {Function} filter Extra params.
 * @return {{}} query.
 */
export function getTableQuery( filter = ( x ) => x ) {
	const query = getQuery();

	return filter(
		pickBy(
			{
				...query,
				paged: parseInt( query.paged, 10 ) || 1,
				per_page: parseInt( query.per_page, 10 ) || 20,
			},
			identity
		)
	);
}

/**
 * This function returns an event handler for the given `param`
 *
 * @param {string} param The parameter in the querystring which should be updated (ex `page`, `per_page`)
 * @param {string} path Relative path (defaults to current path).
 * @param {string} query object of current query params (defaults to current querystring).
 * @return {Function} A callback which will update `param` to the passed value when called.
 */
export function onQueryChange( param, path = getPath(), query = getQuery() ) {
	switch ( param ) {
		case 'sort':
			return ( sort ) => updateQueryString( sort, path, query );
		default:
			return ( value ) =>
				updateQueryString( { [ param ]: value }, path, query );
	}
}

/**
 * Updates the query parameters of the current page.
 *
 * @param {Object} query object of params to be updated.
 * @param {string} path Relative path (defaults to current path).
 * @param {Object} currentQuery object of current query params (defaults to current querystring).
 */
export function updateQueryString(
	query,
	path = getPath(),
	currentQuery = getQuery()
) {
	const newPath = generatePath( query, path, currentQuery );
	getHistory().push( newPath );
}

/**
 * Remove query ags
 *
 * @param {string | Array}key
 * @param {Object} query
 */
export function removeQueryArgs( key, query = getQuery() ) {
	return omit( query, Array.isArray( key ) ? key : [ key ] );
}

/**
 * Adds a listener that runs on history change.
 *
 * @param {Function} listener Listener to add on history change.
 * @return {Function} Function to remove listeners.
 */
export const addHistoryListener = ( listener ) => {
	// Monkey patch pushState to allow trigger the pushstate event listener.
	if ( window.wcNavigation && ! window.wcNavigation.historyPatched ) {
		( ( history ) => {
			/* global CustomEvent */
			const pushState = history.pushState;
			const replaceState = history.replaceState;
			history.pushState = function ( state ) {
				const pushStateEvent = new CustomEvent( 'pushstate', {
					state,
				} );
				window.dispatchEvent( pushStateEvent );
				return pushState.apply( history, arguments );
			};
			history.replaceState = function ( state ) {
				const replaceStateEvent = new CustomEvent( 'replacestate', {
					state,
				} );
				window.dispatchEvent( replaceStateEvent );
				return replaceState.apply( history, arguments );
			};
			window.wcNavigation.historyPatched = true;
		} )( window.history );
	}
	/*eslint-disable @wordpress/no-global-event-listener */
	window.addEventListener( 'popstate', listener );
	window.addEventListener( 'pushstate', listener );
	window.addEventListener( 'replacestate', listener );

	return () => {
		window.removeEventListener( 'popstate', listener );
		window.removeEventListener( 'pushstate', listener );
		window.removeEventListener( 'replacestate', listener );
	};

	/* eslint-enable @wordpress/no-global-event-listener */
};

export * from './filters';
