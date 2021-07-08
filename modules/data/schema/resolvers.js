/**
 * Internal dependencies
 */
/**
 * External dependencies
 */
import { setWith, clone, get } from 'lodash';
import { apiFetch, resolveSelect } from '../base-controls';
import { receiveSchema } from './actions';
import { API_NAMESPACE } from '../localized-data';
import { STORE_NAME } from './constants';
import { getRouteIds, simplifyRouteWithId } from './utils';

/**
 * Resolver for getRoute
 */
export function* getRoute() {
	yield resolveSelect( STORE_NAME, 'fetchSchema' );
}

/**
 * Resolver for fetchSchema
 */
export function* getSchema() {
	yield resolveSelect( STORE_NAME, 'fetchSchema' );
}

/**
 * Resolver for the fetchSchema
 */
export function* fetchSchema() {
	const schemaResponse = yield apiFetch( { path: API_NAMESPACE } );
	const routes =
		schemaResponse && schemaResponse.routes ? schemaResponse.routes : {};
	const reducer = Object.keys( routes ).reduce( ( acc = {}, route ) => {
		const resourceName = route
			.replace( `${ API_NAMESPACE }/`, '' )
			.replace( /\/\(\?P\<[a-z_]*\>\[\\*[a-z][\+\-]?\]\+\)/g, '' );
		if ( resourceName && resourceName !== API_NAMESPACE ) {
			const routeIdNames = getRouteIds( route );
			const savedRoute = simplifyRouteWithId( route, routeIdNames );
			const endpoints = get( routes[ route ], [ 'endpoints' ], [] );
			const routedSchema = endpoints.reduce( ( memo = {}, route ) => {
				const args = get( route, [ 'args' ], {} );
				const methods = get( route, [ 'methods' ], [] );
				[ 'GET', 'PUT', 'POST', 'DELETE' ].forEach( ( method ) => {
					if ( methods.includes( method ) ) {
						memo = setWith(
							clone( memo ),
							[ method ],
							args,
							clone
						);
					}
				} );
				return { ...memo };
			}, [] );
			acc = setWith(
				clone( acc ),
				[ resourceName, savedRoute ],
				{ routeIdNames, ...routedSchema },
				clone
			);
		}
		return { ...acc };
	}, [] );
	yield receiveSchema( reducer );
}
