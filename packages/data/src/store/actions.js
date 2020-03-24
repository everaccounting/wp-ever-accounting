/**
 * Internal dependencies
 */
import {ACTION_TYPES as types} from './action-types';
import {select, dispatch} from '@wordpress/data-controls';
import {STORE_KEY} from "./constants";
import {isArray, isEmpty} from 'lodash';

/**
 * Action creator for setting a single query-state value for a given context.
 *
 * @param {string} context  Context for query state being stored.
 * @param {string} queryKey Key for query item.
 * @param {*}      value    The value for the query item.
 *
 * @return {Object} The action object.
 */
export const setQuery = (context, queryKey, value) => {
	return {
		type: types.SET_QUERY,
		context,
		queryKey,
		value,
	};
};

/**
 * Action creator for setting query-state for a given context.
 *
 * @param {string} context Context for query state being stored.
 * @param {*}      value   Query state being stored for the given context.
 *
 * @return {Object} The action object.
 */
export const setContextQuery = (context, value) => {
	return {
		type: types.SET_CONTEXT_QUERY,
		context,
		value,
	};
};

/**
 * Action creator for resetting query-state for a given context.
 * @param context Context for query state being stored.
 * @returns {{context: *, type: string}}
 */
export const resetQuery = (context) => {
	return {
		type: types.RESET_CONTEXT_QUERY,
		context
	};
};

/**
 * Returns an action object used to update the store with the provided list
 * of model routes.
 *
 * @param {Object} routes An array of routes to add to the store state.
 * @returns {{routes: *, type: string}}
 */
export function receiveRoutes(routes) {
	return {
		type: types.RECEIVE_ROUTES,
		routes,
	};
}

let Headers = window.Headers || null;
Headers = Headers ? new Headers() : {get: () => undefined, has: () => undefined};

/**
 * Returns an action object used in updating the store with the provided items
 * retrieved from a request using the given querystring.
 *
 * @param resourceName
 * @param queryString
 * @param ids
 * @param response
 * @returns {{response: *, query: string, ids: *[], resourceName: *, type: string}}
 */
export function receiveCollection(resourceName, queryString = '', ids = [], response) {
	return {
		type: types.RECEIVE_COLLECTION,
		resourceName,
		queryString,
		ids,
		response,
	};
}

/**
 * Receive Collection
 *
 * @param resourceName
 * @returns {*}
 */
export function resetCollection(resourceName) {
	return {
		type: types.RESET_COLLECTION, resourceName, response: {},
	};
}

/**
 * Receive Collection Error
 *
 * @param resourceName
 * @param queryString
 * @param ids
 * @param error
 * @returns {{response: {headers: {prototype: Headers; new(init?: HeadersInit): Headers} | null, error: *, items: []}, ids: *, resourceName: *, type: string, queryString: *}}
 */
export function receiveCollectionError(resourceName, queryString, ids, error) {
	return {
		type: types.COLLECTION_ERROR, resourceName, queryString, ids, response: {
			items: [],
			headers: Headers,
			error,
		},
	};
}


/**
 * Action for resetting store
 *
 * @param selectorName
 * @returns {Generator<*, void, ?>}
 */
export function* resetStore(selectorName = 'getCollection') {
	// get resolvers
	const resolvers = yield select(
		'ea/store',
		'getCachedResolvers'
	);

	for (const selector in resolvers) {
		if (selectorName === selector) {
			for (const entry of resolvers[selector]._map) {
				yield dispatch(
					'ea/store',
					'invalidateResolution',
					selector,
					entry[0],
				);
			}
		}
	}
}


/**
 *
 * @param resourceName
 * @param {Object} data
 * @param {Boolean} reset
 * @returns {Generator<Generator<*, void, ?>|*, *, ?>}
 */
export function* create(resourceName, data = {}, reset = true) {
	const route = yield select(STORE_KEY, 'getRoute', resourceName);

	if (!route) {
		return;
	}
	try {
		const item = yield apiFetch({
			path: route,
			method: 'POST',
			data,
			cache: 'no-store',
		});
		if (reset)
			yield resetStore();
		return item;
	} catch (error) {
		alert(error.message);
	}
}


/**
 * Update entity
 * @param resourceName
 * @param {number} id
 * @param {Object} data
 * @param {Boolean} reset
 * @returns {Generator<Generator<*, void, ?>|*, void, ?>}
 */
export function* update(resourceName, id, data = {}, reset = true) {
	const route = yield select(STORE_KEY, 'getRoute', resourceName, [id]);

	if (!route) {
		return;
	}

	try {
		const item = yield apiFetch({
			path: route,
			method: 'POST',
			data,
			cache: 'no-store',
		});

		if (reset)
			yield resetStore();
		return item;
	} catch (error) {
		alert(error.message);
	}
}



/**
 * Remove
 * @param resourceName
 * @param id
 * @param {Boolean} reset
 * @returns {Generator<Generator<*, void, ?>|*, void, ?>}
 */
export function* remove(resourceName, id, reset = true) {
	const ids = isArray(id) ? id : [id];
	const resource = `${resourceName}/bulk`;

	if (isEmpty(ids)) {
		return;
	}

	const route = yield select(STORE_KEY, 'getRoute', resource);

	if (!route) {
		return;
	}

	try {
		yield apiFetch({
			path: route,
			method: 'POST',
			cache: 'no-store',
			data: {action: 'delete', items: ids}
		});

		if (reset)
			yield resetStore();
	} catch (error) {
		alert(error.message);
	}
}

/**
 * action creator for model
 * @param modelName
 * @param model
 * @returns {{modelName: *, factory: *, type: string}}
 */
export function receiveModel(modelName, model) {
	return {
		type: types.RECEIVE_MODEL,
		modelName,
		model
	};
}
