/**
 * External dependencies
 */
/**
 * WordPress dependencies
 */
import {combineReducers} from '@wordpress/data';

/**
 * Internal dependencies
 */
import {ACTION_TYPES as types} from './action-types';
import {extractResourceNameFromRoute, getRouteIds, simplifyRouteWithId} from './utils';
import {hasInState, updateState} from '../utils';
import {API_NAMESPACE} from './constants';

/**
 * Reducer for routes
 *
 * @param {Object} state  The current state.
 * @param {Object} action The action object for parsing.
 *
 * @return {Object} The new (or original) state.
 */
export const receiveRoutes = (state = {}, action) => {
	const {type, routes} = action;
	if (type === types.RECEIVE_MODEL_ROUTES) {
		routes.forEach(route => {
			const resourceName = extractResourceNameFromRoute(API_NAMESPACE, route);
			if (resourceName && resourceName !== API_NAMESPACE) {
				const routeIdNames = getRouteIds(route);
				const savedRoute = simplifyRouteWithId(route, routeIdNames);
				if (!hasInState(state, [resourceName, savedRoute])) {
					state = updateState(state, [resourceName, savedRoute], routeIdNames);
				}
			}
		});
	}
	return state;
};

/**
 * Reducer for Schema
 * @param state
 * @param action
 * @returns {{}}
 */
export const receiveSchema = (state = {}, action) => {
	const {type, resourceName, schema} = action;
	if (type === types.RECEIVE_MODEL_SCHEMA) {
		if (!hasInState(state, [resourceName])) {
			state = updateState(state, [resourceName], schema)
		}
	}
	return state;
};

/**
 * Reducer for models
 * @param state
 * @param action
 * @returns {{}}
 */

export const receiveModels = (state = {}, action) => {
	const {type, modelName, model} = action;
	if (type === types.RECEIVE_FACTORY_FOR_MODEL) {

		if (!hasInState(state, [modelName])) {
			state = updateState(state, [modelName], model)
		}
	}

	return state;
};

/**
 * Combined reducers
 */
export default combineReducers({
	routes: receiveRoutes,
	schema: receiveSchema,
	models: receiveModels,
});
