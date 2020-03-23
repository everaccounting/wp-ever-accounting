import {ACTION_TYPES as types} from "./action-types";
import {combineReducers} from '@wordpress/data';
import {isEmpty} from "lodash";
import {extractResourceNameFromRoute, getRouteIds, simplifyRouteWithId} from './utils';
import {API_NAMESPACE} from "./constants";
import {hasInState, updateState} from "../utils";

/**
 * Reducer for processing actions related to the query state store.
 *
 * @param {Object} state  Current state in store.
 * @param {Object} action Action being processed.
 */
export const queryReducer = (state = {}, action) => {
	const {type, context, query = {}} = action;
	const queryKey = Object.keys(query)[0];
	const queryValue = Object.values(query)[0];
	const prevState = typeof state[context] === 'undefined' ? null : state[context];

	let newState;
	switch (type) {
		case types.SET_QUERY:
			const prevStateObject = prevState !== null ? JSON.parse(prevState) : {};
			if (isEmpty(queryValue)) {
				delete prevStateObject[queryKey];
			} else {
				prevStateObject[queryKey] = queryValue;
			}
			newState = JSON.stringify(prevStateObject);
			if (prevState !== newState) {
				state = {...state, [context]: newState};
			}
			break;
		case types.SET_CONTEXT_QUERY:
			newState = JSON.stringify(queryValue);
			if (prevState !== newState) {
				state = {...state, [context]: newState};
			}
			break;

		case types.RESET_CONTEXT_QUERY:
			state = {...state, [context]: null};
	}

	return state;
};

/**
 * Reducer for processing actions related to the routes store.
 *
 * @param {Object} state  Current state in store.
 * @param {Object} action Action being processed.
 */
export const routesReducer = (state = {}, action) => {
	const {type, routes} = action;
	switch (type) {
		case types.RECEIVE_ROUTES:
			routes.forEach(route => {
				const resourceName = extractResourceNameFromRoute(route);
				if (resourceName && resourceName !== API_NAMESPACE) {
					const routeIdNames = getRouteIds(route);
					const savedRoute = simplifyRouteWithId(route, routeIdNames);
					if (!hasInState(state, [resourceName, savedRoute])) {
						state = updateState(state, [resourceName, savedRoute], routeIdNames);
					}
				}
			});
			return state;
		default:
			return state;
	}
};

/**
 * Reducer for processing actions related to the collection store.
 *
 * @param {Object} state  Current state in store.
 * @param {Object} action Action being processed.
 */
export const collectionReducer = (state = {}, action) => {
	const {type, resourceName, queryString, response} = action;
	const ids = action.ids ? JSON.stringify(action.ids) : '[]';
	switch (type) {
		case types.RECEIVE_COLLECTION:
			if (!hasInState(state, [resourceName, ids, queryString])) {
				state = updateState(state, [resourceName, ids, queryString], response);
			}
			break;
		case types.RESET_COLLECTION:
			state = updateState(state, [resourceName, ids, queryString], response);
			break;
		case types.COLLECTION_ERROR:
			state = updateState(state, [resourceName, ids, queryString], response);
			break;
	}
	return state;
};

/**
 * Reducer for processing actions related to the model store.
 *
 * @param {Object} state  Current state in store.
 * @param {Object} action Action being processed.
 */
export const receiveModels = (state = {}, action) => {
	const {type, modelName, model} = action;
	if (type === types.RECEIVE_MODEL) {

		if (!hasInState(state, [modelName])) {
			state = updateState(state, [modelName], model)
		}
	}

	return state;
};


/**
 * Combiner reducer
 */
export default combineReducers({
	queries: queryReducer,
	routes: routesReducer,
	collection: collectionReducer,
	models: receiveModels,
});
