import {fetch, resolveSelect} from '../base-controls';
import {receiveRoutes} from "./actions";
import {REDUCER_KEY} from "./constants";
import {API_NAMESPACE} from "./constants";
/**
 * Resolver for getRoute
 * @returns {Generator<*, void, ?>}
 */
export function* getRoute() {
	yield resolveSelect(
		REDUCER_KEY,
		'getRoutes'
	);
}

/**
 * Resolver for the getRoutes
 * @returns {Generator<Object|{routes: Object, type: string}|{}|{routes: Object, type: string}|{routes: Object, type: string}|*, void, ?>}
 */
export function* getRoutes() {
	const routeResponse = yield fetch({path: API_NAMESPACE});
	const routes = routeResponse && routeResponse.routes ? Object.keys(routeResponse.routes) : [];
	yield receiveRoutes(routes);
}
