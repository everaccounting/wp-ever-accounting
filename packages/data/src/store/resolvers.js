import {STORE_KEY, API_NAMESPACE} from "./constants";
import {select, apiFetch} from '@wordpress/data-controls';
import {receiveRoutes} from "./actions";
import {receiveCollection, receiveCollectionError} from "./actions";
import {apiFetchCollection} from "./controls";
import {addQueryArgs} from '@wordpress/url';
import {pluralModelName} from "./utils";
import {isSchemaResponseOfModel} from "../validators";
import {createEntityFactory} from "../factory";
import {receiveModel} from "./actions";

/**
 * Resolver for getRoute
 * @returns {Generator<*, void, ?>}
 */
export function* getRoute() {
	yield select(STORE_KEY, 'getRoutes');
}

/**
 * Resolver for the getRoutes
 * @returns {Generator<Object|{routes: Object, type: string}|{}|{routes: Object, type: string}|{routes: Object, type: string}|*, void, ?>}
 */
export function* getRoutes() {
	const routeResponse = yield apiFetch({path: API_NAMESPACE});
	const routes = routeResponse && routeResponse.routes ? Object.keys(routeResponse.routes) : [];
	yield receiveRoutes(routes);
}

/**
 * Resolver for retrieving a collection via a api route.
 *
 * @param {string} resourceName
 * @param {Object} query
 * @param {Array}  ids
 */
export function* getCollection(resourceName, query = null, ids = []) {
	const route = yield select(STORE_KEY, 'getRoute', resourceName, ids);
	const queryString = addQueryArgs('', query);
	if (!route) {
		yield receiveCollection(resourceName, queryString, ids);
		return;
	}

	try {
		const {items = [], headers} = yield apiFetchCollection(route + queryString);
		yield receiveCollection(resourceName, queryString, ids, {items, headers});
	} catch (error) {
		yield receiveCollectionError(resourceName, queryString, ids, error);
	}
}

/**
 * Resolver for retrieving a specific collection header for the given arguments
 *
 * Note: This triggers the `getCollection` resolver if it hasn't been resolved
 * yet.
 *
 * @param {string} header
 * @param {string} resourceName
 * @param {Object} query
 * @param {Array}  ids
 */
export function* getCollectionHeader(header, resourceName, query = null, ids = []) {
	const args = [resourceName, query].filter(arg => typeof arg !== 'undefined');
	yield select(STORE_KEY, 'getCollection', ...args);
}

/**
 * Resolver for retrieving a specific collection total for the given arguments
 *
 * Note: This triggers the `getCollection` resolver if it hasn't been resolved
 * yet.
 *
 * @param {string} resourceName
 * @param {Object} query
 * @param {Array}  ids
 */
export function* getTotal(resourceName, query = null, ids = []) {
	const args = [resourceName, query].filter(arg => typeof arg !== 'undefined');
	yield select(STORE_KEY, 'getCollection', ...args);
}

/**
 * Resolver for retrieving a specific collection status for the given arguments
 *
 * Note: This triggers the `getCollection` resolver if it hasn't been resolved
 * yet.
 *
 * @param {string} resourceName
 * @param {Object} query
 * @param {Array}  ids
 */
export function* getCollectionStatus(resourceName, query = null, ids = []) {
	const args = [resourceName, query].filter(arg => typeof arg !== 'undefined');
	yield select(STORE_KEY, 'getCollection', ...args);
}


/***
 *
 * @param modelName
 */
export function* getModel(modelName) {
	const resourceName = pluralModelName(modelName);
	const schemaResponse = yield apiFetch({path: `${API_NAMESPACE}/${resourceName}`, method: "OPTIONS"});
	const response = schemaResponse && schemaResponse.schema && schemaResponse.schema ? schemaResponse : {};
	if (!isSchemaResponseOfModel(response, modelName)) {
		return null;
	}

	const model = createEntityFactory(
		modelName,
		response.schema
	);

	yield receiveModel(modelName, model);
	return model;
}
