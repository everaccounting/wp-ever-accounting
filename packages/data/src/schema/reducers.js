/**
 * External dependencies
 */
/**
 * WordPress dependencies
 */
import { combineReducers } from '@wordpress/data';

/**
 * Internal dependencies
 */
import { ACTION_TYPES as types } from './action-types';
import { extractResourceNameFromRoute, getRouteIds, simplifyRouteWithId } from './utils';
import { hasInState, updateState } from '../utils';
import { API_NAMESPACE } from './constants';

/**
 * Reducer for routes
 *
 * @param {Object} state  The current state.
 * @param {Object} action The action object for parsing.
 *
 * @return {Object} The new (or original) state.
 */
export const receiveRoutes = (state = {}, action) => {
	const { type, routes } = action;
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

export default combineReducers({
	routes: receiveRoutes,
});
