/**
 * Internal dependencies
 */
import { hasInState } from '../utils';
/**
 * WordPress dependencies
 */
import { addQueryArgs } from '@wordpress/url';
import { isResolving } from '../base-selectors';
import { REDUCER_KEY } from './constants';

/**
 * A generic function to retrieve from store
 *
 * @param state
 * @param resourceName
 * @param query
 * @param group
 * @param fallback
 * @return {*}
 */
const getFromState = ({ state, resourceName, query, group = [], fallback = [] }) => {
	// prep ids and query for state retrieval
	group = JSON.stringify(group);
	query = query !== null ? addQueryArgs('', query) : '';
	if (hasInState(state, [resourceName, group, query])) {
		return state[resourceName][group][query];
	}
	return fallback;
};

/**
 * Resolver for generic items returned from an endpoint.
 *
 *  @param {Object} state Data state.
 * @param {string} resourceName  The identifier for the items.
 * @param {Object} query
 * the REST request.
 */
export function fetchAPI(state, resourceName, query = null) {
	return getFromState({ state, resourceName, query, group: [], fallback: {} });
}

/**
 * Resolver for getCollection selection this is used in the situation
 * where items with total is needed it automatically detects x-wp-total header
 * Best use case is when need all contacts with total may be in list table
 *
 * getCollection('contacts', {type='customer'})
 *
 * @param {Object} state Data state.
 * @param {string} resourceName
 * @param {Object} query
 * @return {Generator<Immutable.Map|{path: string, type: string}|{args: Array[], reducerKey: string, selectorName: string, type: string}, *, ?>}
 */
export function getCollection(state, resourceName, query = null) {
	return getFromState({ state, resourceName, query, group: [], fallback: { items: [], total: NaN } });
}

/**
 * This is same like getCollection but with the feature of calling from url parts
 * like from this url customer/customers
 *
 * getCollection('contacts', ['customers'], {type='customer'})
 
 * @param {Object} state Data state.
 * @param {string} resourceName
 * @param {Array} parts
 * @param {Object} query
 * @return {Generator<Immutable.Map|{path: string, type: string}|{args: Array[], reducerKey: string, selectorName: string, type: string}, *, ?>}
 */
export function getCollectionWithRouteParts(state, resourceName, parts = [], query = null) {
	return getFromState({ state, resourceName, query, group: parts, fallback: { items: [], total: NaN } });
}

/**
 * This is for Calling a single Entry from any simple route
 *
 * getEntityById('contacts', 10, {include:'address'})
 
 * @param {Object} state Data state.
 * @param {string} resourceName
 * @param {number} id
 * @param {Object} query
 * @return {Generator<Immutable.Map|{args: Array[], reducerKey: string, selectorName: string, type: string}|{type: string, request: Object}, *, ?>}
 */
export function getEntityById(state, resourceName, id = null, query = null) {
	return getFromState({ state, resourceName, query, group: [id], fallback: {} });
}

/**
 * This is same as getEntityById with the featured address from complex endpoint
 
 * @param {Object} state Data state.
 * @param {string} resourceName
 * @param {Array} parts
 * @param {number} id
 * @param {Object} query
 * @return {Generator<Immutable.Map|{args: Array[], reducerKey: string, selectorName: string, type: string}|{type: string, request: Object}, *, ?>}
 */
export function getEntitiesWithRouteParts(state, resourceName, parts = [], id = null, query = null) {
	return getFromState({ state, resourceName, query, group: [parts].concat([id]), fallback: {} });
}

/**
 * Helper indicating whether the given resourceName, selectorName, and queryString
 * is being resolved or not.
 *
 * @param {Object} state
 * @param {string} resourceName
 * @param {string} selectorName
 * @param {Object} query
 * @return {boolean} Returns true if the selector is currently requesting items.
 */
export function isRequesting(state, resourceName, selectorName, query = null) {
	return isResolving(REDUCER_KEY, selectorName, resourceName, query);
}

/**
 * Returns whether the items for the given resourceName and query string are being
 * requested.
 *
 * @param {Object} state Data state.
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
 *
 * @param {Object} state
 * @param {string} resourceName
 * @param {Object} query
 * @return {boolean} True means entities (for the given model) are being
 * requested.
 */
export function isRequestingGetCollection(state, resourceName, query = null) {
	return isRequesting(state, resourceName, 'getCollection', query);
}

/**
 * Returns whether the get entities request is in the process of being resolved
 * or not.
 *
 * @param {Object} state
 * @param {string} resourceName
 * @param {number} id
 * @param {Object} query
 * @return {boolean} True means entities (for the given model) are being
 * requested.
 */
export function isRequestingGetEntityById(state, resourceName, id, query = null) {
	return isRequesting(state, resourceName, 'getEntityById', id, query);
}
