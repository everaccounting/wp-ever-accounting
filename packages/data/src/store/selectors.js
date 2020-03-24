import {hasInState, getRouteFromResourceEntries, getFromState} from "./utils";
import {STORE_KEY} from "./constants";
import {createRegistrySelector, select} from '@wordpress/data';
import {isEmpty} from "lodash";


/**
 * Selector for retrieving a specific query-state for the given context.
 *
 * @param {Object} state    Current state.
 * @param {string} context  Context for the query-state being retrieved.
 * @param {string} queryKey Key for the specific query-state item.
 * @param {*} defaultValue  Default value for the query-state key if it doesn't
 *                          currently exist in state.
 *
 * @return {*} The currently stored value or the defaultValue if not present.
 */


export const getQueryByKey = (state, context, queryKey, defaultValue = {}) => {
	let stateContext = typeof state.queries[context] === 'undefined' ? null : state.queries[context];

	if (stateContext === null) {
		return defaultValue;
	}
	stateContext = JSON.parse(stateContext);
	return typeof stateContext[queryKey] !== 'undefined' ? stateContext[queryKey] : defaultValue;
};

/**
 * Selector for retrieving the query state for the give context
 * @param state
 * @param context
 * @param defaultValue
 * @returns {*}
 */
export const getQuery = (state, context, defaultValue = {page: 1, per_page: 20}) => {
	const stateContext = typeof state.queries[context] === 'undefined' ? null : state.queries[context];
	return stateContext === null ? defaultValue : JSON.parse(stateContext);
};


/**
 * returns route for the resource name & id is the url part of the request
 *
 * Ids example:
 * If you are looking for the route for a single contact on the `contacts`
 * resourceName, then you'd have `[ 10 ]` as the ids.  This would produce something
 * like `/ea/v1/contacts/20`
 * Or for a settings resourceName you would call ['general'] then it will convert as
 * `/ea/v1/settings/general`
 *
 * @param state
 * @param resourceName
 * @param ids
 * @returns {string|*}
 */
export const getRoute = createRegistrySelector(select => (state, resourceName, ids = []) => {
	const hasResolved = select(STORE_KEY).hasFinishedResolution('getRoutes');
	state = state.routes;
	let error = '';
	if (!state[resourceName]) {
		error = sprintf('There is no route for the given resource name (%s) in the store', resourceName);
	}

	if (error !== '') {
		if (hasResolved) {
			throw new Error(error);
		}
		return '';
	}

	const route = getRouteFromResourceEntries(state[resourceName], ids);

	if (route === '') {
		if (hasResolved) {
			throw new Error(
				sprintf(
					'While there is a route for the given  resource name (%s), there is no route utilizing the number of ids you included in the select arguments. The available routes are: (%s)',
					resourceName,
					JSON.stringify(state[resourceName])
				)
			);
		}
	}
	return route;
});

/**
 * Return all the routes in store.
 * @param state
 * @return {Array} An array of all routes.
 */
export const getRoutes = createRegistrySelector(select => state => {
	const hasResolved = select(STORE_KEY).hasFinishedResolution('getRoutes');
	state = state.routes;
	if (!state) {
		if (hasResolved) {
			throw new Error(sprintf('There is no route for the given namespace (%s) in the store', '/ea/v1'));
		}
		return [];
	}

	let namespaceRoutes = [];
	for (const resourceName in state) {
		namespaceRoutes = [...namespaceRoutes, ...Object.keys(state[resourceName])];
	}

	return namespaceRoutes;
});


/**
 * Retrieves the collection items from the state for the given arguments.
 *
 * @param {Object} state        The current collections state.
 * @param {string} resourceName The resource name for the collection.
 * @param {Object} [query=null] The query for the collection request.
 * @param {Array}  [ids=[]]     Any ids for the collection request (these are
 *                              values that would be added to the route for a
 *                              route with id placeholders)
 * @return {Array} an array of items stored in the collection.
 */
export const getCollection = (state, resourceName, query = null, ids = []) => {
	state = state.collection;
	return getFromState({
		state,
		resourceName,
		query,
		ids,
		type: 'items',
		fallback: [],
	});
};

/**
 * Retrieves the collection headers from the state for the given arguments.
 *
 * @param {Object} state        The current collections state.
 * @param {string} resourceName The resource name for the collection.
 * @param {Object} [query=null] The query for the collection request.
 * @param {Array}  [ids=[]]     Any ids for the collection request (these are
 *                              values that would be added to the route for a
 *                              route with id placeholders)
 * @return {Array} an array of items stored in the collection.
 */
export const getCollectionHeaders = (state, resourceName, query = null, ids = []) => {
	state = state.collection;
	return getFromState({
		state,
		resourceName,
		query,
		ids,
		type: 'headers',
		fallback: {},
	});
};

/**
 * This selector enables retrieving a specific header value from a given
 * collection request.
 * @param state
 * @param header
 * @param resourceName
 * @param query
 * @param ids
 * @returns {null|*}
 */
export const getCollectionHeader = (state, header, resourceName, query = null, ids = []) => {
	const headers = getCollectionHeaders(state, resourceName, query, ids);
	if (headers && headers.get) {
		return headers.has(header) ? headers.get(header) : undefined;
	}
	return null;
};
/***
 * Get error
 *
 * @param state
 * @param resourceName
 * @param query
 * @param ids
 * @returns {*}
 */
export const getCollectionError = (state, resourceName, query = null, ids = []) => {
	state = state.collection;
	return getFromState({
		state,
		resourceName,
		query,
		ids,
		type: 'error',
		fallback: null,
	});
};

/**
 * Get total
 * @param state
 * @param resourceName
 * @param query
 * @param ids
 * @returns {*}
 */
export const getTotal = (state, resourceName, query = null, ids = []) => {
	return getCollectionHeader(state, 'x-wp-total', resourceName, query, ids);
};

/**
 * Get Status
 * @param state
 * @param resourceName
 * @param query
 * @param ids
 */
export const getCollectionStatus = (state, resourceName, query = {}, ids = []) => {
	const error = getCollectionError(state, resourceName, query, ids);
	const args = [resourceName, query];
	const resolving = select(STORE_KEY).isResolving('getCollection', args);
	let status;
	if (!error && resolving === true) {
		status = "STATUS_IN_PROGRESS";
	} else if (!error && resolving !== true) {
		status = "STATUS_COMPLETE"
	} else if (error) {
		status = "STATUS_FAILED"
	}

	return status;
};


/**
 * Selector for returning the model entity object for a given
 * model name from the state.
 *
 * @param {Object} state
 * @param {string} modelName
 * @return {Object} Returns the model entity or null if it doesn't
 * exist.
 */
export const getModel = (state, modelName) => {
	state = state.models;
	if (hasInState(state, [modelName])) {
		return state[modelName];
	}
	return null;
};
