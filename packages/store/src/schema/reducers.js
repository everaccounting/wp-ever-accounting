/**
 * Internal imports
 */
/**
 * WordPress dependencies
 */
import { combineReducers } from '@wordpress/data';
/**
 * Internal dependencies
 */
import { ACTION_TYPES as types } from './action-types';
import { API_NAMESPACE } from './constants';
import { getRouteIds, simplifyRouteWithId } from './utils';
import { hasInState, updateState } from '../utils';

/**
 * Reducer for processing actions related to the routes store.
 *
 * @param {Object} state  Current state in store.
 * @param {Object} action Action being processed.
 */
export const receiveRoutes = (state = {}, action) => {
	const { type, routes } = action;
	switch (type) {
		case types.RECEIVE_ROUTES:
			routes.forEach(route => {
				const resourceName = route.replace(`${API_NAMESPACE}/`, '').replace(/\/\(\?P\<[a-z_]*\>\[\\*[a-z]\]\+\)/g, '');
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
 * Be aware that the root state is a plain object but each slice ('schema',
 * 'factory', 'routes') is an immutable Map.
 */
export default combineReducers({
	routes: receiveRoutes,
});
