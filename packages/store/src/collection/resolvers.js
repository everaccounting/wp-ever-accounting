import {isEmpty} from 'lodash';
import {receiveResponse, receiveEntityResponse, receiveEntitiesById} from './actions';
import {fetch, fetchFromAPIWithTotal, resolveSelect, select} from '../base-controls';
import {REDUCER_KEY as COLLECTION_REDUCER_KEY} from './constants';
import {REDUCER_KEY as SCHEMA_REDUCER_KEY} from '../schema/constants';
import {addQueryArgs} from '@wordpress/url';

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
 * Resolver for model entities returned from an endpoint.
 * @param {string} resourceName
 * @param {Object} query
 * @return {IterableIterator<*>|Array<BaseEntity>} An empty array if no
 * entities retrieved.
 */
export function* getEntities(resourceName, query = null,) {
	const queryString = addQueryArgs('', query);
	const route = yield resolveSelect(SCHEMA_REDUCER_KEY, 'getRoute', resourceName);
	const response = yield fetchFromAPIWithTotal(route + queryString);
	yield receiveEntityResponse(resourceName, queryString, response);
	return response;
}

/**
 * Resolver for getting model entities for a given set of ids
 * @param {string} resourceName
 * @param {Array<any>}ids
 * @param {Object} query
 * @return {IterableIterator<*>|Array} An empty array if no entities retrieved.
 */
export function* getEntitiesByIds(resourceName, ids = [], query = null) {
	const queryString = addQueryArgs('', query);
	const route = yield resolveSelect(SCHEMA_REDUCER_KEY, 'getRoute', resourceName, ids);
	const response = yield fetch({path: route + queryString});
	yield receiveEntitiesById(resourceName, ids, queryString, response);
}
