/**
 * External dependencies
 */
/**
 * WordPress dependencies
 */
import { select, apiFetch } from '@wordpress/data-controls';

/**
 * Internal dependencies
 */
import { receiveRoutes } from './actions';
import { STORE_KEY } from './constants';
import { API_NAMESPACE } from './constants';

/**
 * Resolver for the getRoute selector.
 *
 * Note: All this essentially does is ensure the routes for the given namespace
 * have been resolved.
 *
 */
export function* getRoute() {
	// we call this simply to do any resolution of all endpoints if necessary.
	// allows for jit population of routes for a given namespace.
	yield select(STORE_KEY, 'getRoutes');
}

/**
 * Resolver for the getRoutes selector.
 *
 */
export function* getRoutes() {
	const routeResponse = yield apiFetch({ path: API_NAMESPACE });
	console.log(routeResponse);
	const routes = routeResponse && routeResponse.routes ? Object.keys(routeResponse.routes) : [];
	yield receiveRoutes(routes);
}
