/**
 * WordPress dependencies
 */
import { createRegistrySelector } from '@wordpress/data';
import { sprintf } from '@wordpress/i18n';

/**
 * Internal dependencies
 */
import { STORE_NAME } from './constants';
import { getRouteFromResourceEntries, getParamsFromSchema } from './utils';

/**
 * returns route for the resource name & id is the url part of the request
 *
 * Ids example:
 * If you are looking for the route for a single contact on the `contacts`
 * resourceName, then you'd have `[ 10 ]` as the ids.  This would produce something
 * like `/ea/v1/contacts/20`
 * Or for a settings resourceName you would call ['general'] then it will convert as
 * `/ea/v1/settings/general`
 *
 * @param state
 * @param resourceName
 * @param ids
 * @return {string|*}
 */
export const getRoute = createRegistrySelector(
	( select ) => ( state, resourceName, ids = [] ) => {
		const hasResolved = select( STORE_NAME ).hasFinishedResolution(
			'fetchSchema'
		);
		let error = '';
		if ( ! state[ resourceName ] ) {
			error = sprintf(
				'There is no route for the given resource name (%s) in the store',
				resourceName
			);
		}

		if ( error !== '' ) {
			if ( hasResolved ) {
				throw new Error( error );
			}
			return '';
		}

		const route = getRouteFromResourceEntries( state[ resourceName ], ids );
		if ( route === '' ) {
			if ( hasResolved ) {
				throw new Error(
					// eslint-disable-next-line @wordpress/valid-sprintf
					sprintf(
						'While there is a route for the given  resource name (%s), there is no route utilizing the number of ids you included in the select arguments. The available routes are: (%s)',
						resourceName,
						JSON.stringify( state[ resourceName ] )
					)
				);
			}
		}
		return route;
	}
);

export const getSchema = createRegistrySelector(
	( select ) => ( state, resourceName, ids = [], method = 'POST' ) => {
		const hasResolved = select( STORE_NAME ).hasFinishedResolution(
			'fetchSchema'
		);
		let error = '';
		if ( ! state[ resourceName ] ) {
			error = sprintf(
				'There is no route for the given resource name (%s) in the store',
				resourceName
			);
		}

		if ( error !== '' ) {
			if ( hasResolved ) {
				throw new Error( error );
			}
			return '';
		}

		const paramsFromSchema = getParamsFromSchema(
			state[ resourceName ],
			ids,
			method
		);
		if ( paramsFromSchema === {} ) {
			if ( hasResolved ) {
				throw new Error(
					// eslint-disable-next-line @wordpress/valid-sprintf
					sprintf(
						'While there is a route for the given  resource name (%s), there is no route utilizing the number of ids you included in the select arguments. The available routes are: (%s)',
						resourceName,
						JSON.stringify( state[ resourceName ] )
					)
				);
			}
		}
		return paramsFromSchema;
	}
);

/**
 * Return all the routes in store.
 *
 * @param state
 * @return {Array} An array of all routes.
 */
export const fetchSchema = createRegistrySelector( ( select ) => ( state ) => {
	const hasResolved = select( STORE_NAME ).hasFinishedResolution(
		'fetchSchema'
	);
	if ( ! state ) {
		if ( hasResolved ) {
			throw new Error(
				sprintf(
					'There is no route for the given namespace (%s) in the store',
					'/ea/v1'
				)
			);
		}
		return [];
	}

	let namespaceRoutes = [];
	for ( const resourceName in state ) {
		namespaceRoutes = [
			...namespaceRoutes,
			...Object.keys( state[ resourceName ] ),
		];
	}

	return namespaceRoutes;
} );
