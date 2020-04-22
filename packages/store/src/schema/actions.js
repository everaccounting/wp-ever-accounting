/**
 * Internal imports
 */
/**
 * Internal dependencies
 */
import { ACTION_TYPES as types } from './action-types';

/**
 * Returns an action object used to update the store with the provided list
 * of model routes.
 *
 * @param {Object} routes An array of routes to add to the store state.
 * @return {{routes: *, type: string}}
 */
export function receiveRoutes(routes) {
	return {
		type: types.RECEIVE_ROUTES,
		routes,
	};
}
