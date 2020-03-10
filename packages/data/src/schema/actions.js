/**
 * Internal dependencies
 */
import { ACTION_TYPES as types } from './action-types.js';
import { API_NAMESPACE } from '../constants';

/**
 * Returns an action object used to update the store with the provided list
 * of model routes.
 *
 * @param   {Object}  routes  An array of routes to add to the store state.
 * @param   {string}  namespace
 *
 * @return  {Object}             The action object.
 */
export function receiveRoutes(routes, namespace = API_NAMESPACE) {
	return {
		type: types.RECEIVE_MODEL_ROUTES,
		routes,
		namespace,
	};
}
