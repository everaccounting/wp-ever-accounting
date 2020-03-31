import {hasInState} from "../utils";
import {addQueryArgs} from '@wordpress/url';

/**
 * A generic function to retrieve from store
 * @param state
 * @param resourceName
 * @param query
 * @param group
 * @param fallback
 * @returns {*}
 */
const getFromState = ({state, resourceName, query, group = [], fallback = []}) => {
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
	return getFromState({state, resourceName, query, group: [], fallback: {}});
}


/**
 * Resolver for getCollection selection this is used in the situation
 * where items with total is needed it automatically detects x-wp-total header
 * Best use case is when need all contacts with total may be in list table
 *
 * getCollection('contacts', {type='customer'})
 *
 * @param {Object} state Data state.
 * @param {String} resourceName
 * @param {Object} query
 * @returns {Generator<Immutable.Map|{path: string, type: string}|{args: Array[], reducerKey: string, selectorName: string, type: string}, *, ?>}
 */
export function getCollection(state, resourceName, query = null) {
	return getFromState({state, resourceName, query, group: [], fallback: {items: [], total: NaN}});
}


/**
 * This is same like getCollection but with the feature of calling from url parts
 * like from this url customer/customers
 *
 * getCollection('contacts', ['customers'], {type='customer'})

 * @param {Object} state Data state.
 * @param {String} resourceName
 * @param {Array} parts
 * @param {Object} query
 * @returns {Generator<Immutable.Map|{path: string, type: string}|{args: Array[], reducerKey: string, selectorName: string, type: string}, *, ?>}
 */
export function getCollectionWithRouteParts(state, resourceName, parts = [], query = null) {
	return getFromState({state, resourceName, query, group: parts, fallback: {items: [], total: NaN}});
}


/**
 * This is for Calling a single Entry from any simple route
 *
 * getEntityById('contacts', 10, {include:'address'})

 * @param {Object} state Data state.
 * @param resourceName
 * @param {Number} id
 * @param {Object} query
 * @returns {Generator<Immutable.Map|{args: Array[], reducerKey: string, selectorName: string, type: string}|{type: string, request: Object}, *, ?>}
 */
export function getEntityById(state, resourceName, id = null, query = null) {
	return getFromState({state, resourceName, query, group: [id], fallback: {}});
}


/**
 * This is same as getEntityById with the featured address from complex endpoint

 * @param {Object} state Data state.
 * @param {String} resourceName
 * @param {Array} parts
 * @param {Number} id
 * @param {Object} query
 * @returns {Generator<Immutable.Map|{args: Array[], reducerKey: string, selectorName: string, type: string}|{type: string, request: Object}, *, ?>}
 */
export function getEntitiesWithRouteParts(state, resourceName, parts = [], id = null, query = null) {
	return getFromState({state, resourceName, query, group: [parts].concat([id]), fallback: {}});
}
