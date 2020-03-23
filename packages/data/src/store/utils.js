/**
 * External dependencies
 */
import {has, setWith, clone} from 'lodash';
import pluralize from "pluralize";
import memoize from 'memize';
import {API_NAMESPACE} from "./constants";
import { addQueryArgs } from '@wordpress/url';

/**
 * Utility for returning whether the given path exists in the state.
 *
 * @param {Object} state The state being checked
 * @param {Array}  path  The path to check
 *
 * @return {boolean} True means this exists in the state.
 */
export const hasInState = (state, path) => {
	return has(state, path);
};


/**
 * Utility for updating state and only cloning objects in the path that changed.
 *
 * @param {Object} state The state being updated
 * @param {Array}  path  The path being updated
 * @param {*}      value The value to update for the path
 *
 * @return {Object} The new state
 */
export const updateState = (state, path, value) => {
	return setWith(clone(state), path, value, clone);
};

/**
 * Used to normalize the plural form of a given model name.
 * @param {string} modelName
 * @return {string}  Ensures the given modelName is its plural form.
 */
export const pluralModelName = memoize(
	(modelName) => pluralize(modelName)
);

/**
 * Used to normalize the singular form of a given model name.
 * @param {string} modelName
 * @return {string} Ensures the given modelName is in its singular form.
 */
export const singularModelName = memoize(
	(modelName) => pluralize.singular(modelName)
);


/**
 *
 * @param stateSlice
 * @param ids
 * @returns {string|any}
 */
export const getRouteFromResourceEntries = (stateSlice, ids = []) => {
	// convert to array for easier discovery
	stateSlice = Object.entries(stateSlice);
	const match = stateSlice.find(([, idNames]) => {
		return ids.length === idNames.length;
	});
	const [matchingRoute, routePlaceholders] = match || [];
	// if we have a matching route, let's return it.
	if (matchingRoute) {
		return ids.length === 0 ? matchingRoute : assembleRouteWithPlaceholders(matchingRoute, routePlaceholders, ids);
	}
	return '';
};

/**
 * For a given route, route parts and ids
 * @param {string} route
 * @param {Array}  routePlaceholders
 * @param {Array}  ids
 * @returns {string} Assembled route.
 */
const assembleRouteWithPlaceholders = (route, routePlaceholders, ids) => {
	routePlaceholders.forEach((part, index) => {
		route = route.replace(`{${part}}`, ids[index]);
	});
	return route;
};


/**
 * This returns a resource name string as an index for a given route.
 *
 * For example:
 * /contacts/(?P<id>[\d]+)
 * returns
 * /contacts
 *
 * @param {string} route
 *
 * @return {string} The resource name extracted from the route.
 */
export const extractResourceNameFromRoute = (route) => {
	route = route.replace(`${API_NAMESPACE}/`, '');
	return route.replace(/\/\(\?P\<[a-z_]*\>\[\\*[a-z]\]\+\)/g, '');
};

/**
 * Returns an array of the identifier for the named capture groups in a given
 * route.
 *
 * For example, if the route was this:
 * /ea/v1/contacts/(?P<id>[\d]+)
 *
 * ...then the following would get returned
 * ['id' ]
 *
 * @param  {string} route - The route to extract identifier names from.
 *
 * @return {Array}  An array of named route identifier names.
 */
export const getRouteIds = route => {
	const matches = route.match(/\<[a-z_]*\>/g);
	if (!Array.isArray(matches) || matches.length === 0) {
		return [];
	}
	return matches.map(match => match.replace(/<|>/g, ''));
};

/**
 * This replaces regex placeholders in routes with the relevant named string
 * found in the matchIds.
 *
 * Something like:
 * /contacts/(?P<id>[\d]+)
 *
 * ..ends up as:
 * /contacts/{id}
 *
 * @param {string} route     The route to manipulate
 * @param {Array}  matchIds  An array of named ids ( [ attribute_id, id ] )
 *
 * @return {string} The route with new id placeholders
 */
export const simplifyRouteWithId = (route, matchIds) => {
	if (!Array.isArray(matchIds) || matchIds.length === 0) {
		return route;
	}
	matchIds.forEach(matchId => {
		const expression = `\\(\\?P<${matchId}>.*?\\)`;
		route = route.replace(new RegExp(expression), `{${matchId}}`);
	});
	return route;
};

/**
 * Get specific entry from store
 *
 * @param state
 * @param resourceName
 * @param query
 * @param ids
 * @param type
 * @param fallback
 * @returns {*}
 */
export const getFromState = ({ state, resourceName, query, ids, type = 'items', fallback = [] }) => {
	// prep ids and query for state retrieval
	ids = JSON.stringify(ids);
	query = query !== null ? addQueryArgs('', query) : '';
	if (hasInState(state, [resourceName, ids, query, type])) {
		return state[resourceName][ids][query][type];
	}
	return fallback;
};
