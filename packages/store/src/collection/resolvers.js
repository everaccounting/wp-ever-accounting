import {receiveResponse, receiveCollection, receiveCollectionWithRouteParts, receiveEntity, receiveEntitiesWithRouteParts} from './actions';
import {fetch, fetchFromAPIWithTotal, resolveSelect} from '../base-controls';
import {REDUCER_KEY as SCHEMA_REDUCER_KEY} from '../schema/constants';
import {addQueryArgs} from '@wordpress/url';
import {isResolving} from "../base-selectors";
import {REDUCER_KEY} from "./constants";

/**
 * Resolver for generic items returned from an endpoint.
 *
 * @param {string} resourceName  The identifier for the items.
 * @param {Object} query
 * the REST request.
 */
export function* fetchAPI(resourceName, query = null) {
	const queryString = addQueryArgs('', query);
	const route = yield resolveSelect(SCHEMA_REDUCER_KEY, 'getRoute', resourceName);
	const response = yield fetch({path: route + queryString});
	yield receiveResponse(resourceName, queryString, response);
}

/**
 * Resolver for getCollection selection this is used in the situation
 * where items with total is needed it automatically detects x-wp-total header
 * Best use case is when need all contacts with total may be in list table
 *
 * getCollection('contacts', {type='customer'})
 *
 * @param {String} resourceName
 * @param {Object} query
 * @returns {Generator<Object|{path: string, type: string}|{args: Array[], reducerKey: string, selectorName: string, type: string}, *, ?>}
 */
export function* getCollection(resourceName, query = null) {
	const queryString = addQueryArgs('', query);
	const route = yield resolveSelect(SCHEMA_REDUCER_KEY, 'getRoute', resourceName);
	const response = yield fetchFromAPIWithTotal(route + queryString);
	yield receiveCollection(resourceName, queryString, response);
	return response;
}


/**
 * This is same like getCollection but with the feature of calling from url parts
 * like from this url customer/customers
 *
 * getCollection('contacts', ['customers'], {type='customer'})
 *
 * @param {String} resourceName
 * @param {Array} parts
 * @param {Object} query
 * @returns {Generator<Object|{path: string, type: string}|{args: Array[], reducerKey: string, selectorName: string, type: string}, *, ?>}
 */
export function* getCollectionWithRouteParts(resourceName, parts = [], query = null) {
	const queryString = addQueryArgs('', query);
	const route = yield resolveSelect(SCHEMA_REDUCER_KEY, 'getRoute', resourceName, parts);
	const response = yield fetchFromAPIWithTotal(route + queryString);
	yield receiveCollectionWithRouteParts(resourceName, parts,queryString, response);
	return response;
}

/**
 * This is for Calling a single Entry from any simple route
 *
 * getEntityById('contacts', 10, {include:'address'})
 *
 * @param resourceName
 * @param {Number} id
 * @param {Object} query
 * @returns {Generator<Object|{args: Array[], reducerKey: string, selectorName: string, type: string}|{type: string, request: Object}, *, ?>}
 */
export function* getEntityById(resourceName, id = null, query = null) {
	const queryString = addQueryArgs('', query);
	const route = yield resolveSelect(SCHEMA_REDUCER_KEY, 'getRoute', resourceName, [id]);
	const response = yield fetch({path: route + queryString});
	yield receiveEntity(resourceName,id, queryString, response);
	return response;
}


/**
 * This is same as getEntityById with the featured address from complex endpoint
 *
 * @param {String} resourceName
 * @param {Array} parts
 * @param {Number} id
 * @param {Object} query
 * @returns {Generator<Object|{args: Array[], reducerKey: string, selectorName: string, type: string}|{type: string, request: Object}, *, ?>}
 */
export function* getEntitiesWithRouteParts(resourceName, parts = [], id = null, query = null) {
	const queryString = addQueryArgs('', query);
	const route = yield resolveSelect(SCHEMA_REDUCER_KEY, 'getRoute', resourceName, [id]);
	const response = yield fetch({path: route + queryString});
	yield receiveEntitiesWithRouteParts(resourceName, queryString, response);
	return response;
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
function isRequesting(state, resourceName, selectorName, query = null) {
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
 * @param {Object} state
 * @param {string} resourceName
 * @param {Object} query
 * @return {boolean} True means entities (for the given model) are being
 * requested.
 */
export function isRequestingGetEntityById(state, resourceName, query = null) {
	return isRequesting(state, resourceName, 'getEntityById', query);
}
