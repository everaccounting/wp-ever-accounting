/**
 * External dependencies
 */
import { useLocation } from 'react-router-dom';
/**
 * Internal dependencies
 */
import { getRoutes } from '../routes';

export function useRoutes() {
	const location = useLocation();
	const currentPath = location.pathname;
	const routes = getRoutes().sort( ( a, b ) => b.path.length - a.path.length );

	let currentRoute = null;
	let matchedRoute = null;
	// What we will do here is to loop through all the routes and find the one that matches the current path.
	// If the current path is /sales/revenues, we will first check if there is a route with path /sales/revenues otherwise we will check if there is a route with path /sales then any child route with path revenues.

	for ( const route of routes ) {
		if ( currentPath === route.path ) {
			currentRoute = route;
			matchedRoute = route;
			break;
		}

		if ( currentPath.startsWith( route.path ) ) {
			currentRoute = route;
		}
	}

	return { currentRoute, matchedRoute, routes };
}
