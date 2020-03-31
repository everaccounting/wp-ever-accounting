import {get} from "lodash";

/**
 * Internal dependencies
 */
import {isResolving} from '../base-selectors';
import {REDUCER_KEY} from './constants';
import {addQueryArgs} from '@wordpress/url';
import {hasInState} from "../utils"

const DEFAULT_EMPTY_ARRAY = [];


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
export const getFromState = ({state, resourceName, query, ids = [], fallback = []}) => {
	// prep ids and query for state retrieval
	ids = JSON.stringify(ids);
	query = query !== null ? addQueryArgs('', query) : '';
	if (hasInState(state, [resourceName, ids, query])) {
		return state[resourceName][ids][query];
	}
	return fallback;
};


/**
 * Returns all the items for the given resourceName and queryString
 *
 * @param {Object} state Data state.
 * @param {string} resourceName The resourceName the items are being retrieved for.
 * @param {Object} query
 * @return {Array} Returns an array of items for the given model and query.
 */
export const fetchAPI = (state, resourceName, query = null) => {
	return getFromState({state, resourceName, query});
};


/**
 * Returns all the model entities for the given modelName and query string.
 *
 * @param {Immutable.Map} state
 * @param {string} resourceName
 * @param {Object} query
 * @return {Array<BaseEntity>} Returns array of entities.
 */
export const getEntities = (state, resourceName, query = null) => {
	return getFromState({state, resourceName, query, ids: [], fallback: {items: [], total: NaN}});
};

/**
 * Returns all the model entities for the given modelName and query string.
 *
 * @param {Immutable.Map} state
 * @param {string} resourceName
 * @param {Array}  [ids=[]]     Any ids for the collection request (these are
 *                              values that would be added to the route for a
 *                              route with id placeholders)
 * @param {Object} query
 * @return {Array<BaseEntity>} An array of entities.
 */
export const getEntitiesByIds = (state, resourceName, ids = [], query = null) => {
	return getFromState({state, resourceName, query, ids, fallback: {}});
};

/**
 * Helper indicating whether the given resourceName, selectorName, and queryString
 * is being resolved or not.
 *
 * @param {Immutable.Map} state
 * @param {string} resourceName
 * @param {string} selectorName
 * @param {Object} query
 * @return {boolean} Returns true if the selector is currently requesting items.
 */
function isRequesting(state, resourceName, selectorName, query = null) {
	return isResolving(REDUCER_KEY, selectorName, resourceName, query);
}


/**
 * Returns whether the items for the given resourceName and query string are being
 * requested.
 *
 * @param {Immutable.Map} state Data state.
 * @param {string} resourceName  The resourceName for the items being requested
 * @param {Object} query
 * @return {boolean} Whether items are being requested or not.
 */
export function isRequestingFetchAPI(state, resourceName, query = null) {
	return isRequesting(state, resourceName, 'fetchAPI', query);
}


/**
 * Returns whether the get entities request is in the process of being resolved
 * or not.
 * @param {Immutable.Map} state
 * @param {string} resourceName
 * @param {Object} query
 * @return {boolean} True means entities (for the given model) are being
 * requested.
 */
export function isRequestingEntities(state, resourceName, query = null) {
	return isRequesting(state, resourceName, 'getEntities', query);
}
