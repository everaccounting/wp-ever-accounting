/**
 * External dependencies
 */
import { get, set } from 'lodash';
import createSelector from 'rememo';
/**
 * Internal dependencies
 */
import { getNormalizedCommaSeparable, getQueryParts } from './utils';

/**
 * Returns the entity config given its kind and name.
 *
 * @param {Object} state Data state.
 * @param {string} name  Entity name.
 *
 * @return {Object} Entity config.
 */
export const getConfig = ( state, name ) => {
	return get( state, [ 'config' ], [] ).find( ( entity ) => entity.name === name );
};

/**
 * Returns the Entity's records.
 *
 * @param {Object} state State tree
 * @param {string} name  Entity name.
 * @param {Object} query Optional terms query. If requesting specific
 *                       fields, fields must always include the ID. For valid query parameters see the [Reference](https://developer.wordpress.org/rest-api/reference/) in the REST API Handbook and select the entity kind. Then see the arguments available for "List [Entity kind]s".
 *
 * @return {Array} Records.
 */
export const getRecords = createSelector( ( state, name, query ) => {
	const { stableKey, page, perPage, include, fields, context = 'default' } = getQueryParts( query );
	console.log( 'stableKey', stableKey );
	let itemIds;
	if ( state.queries?.[ context ]?.[ stableKey ] ) {
		itemIds = state.queries[ context ][ stableKey ];
	}
	console.log( 'getRecords selector' );
	if ( ! itemIds ) {
		return null;
	}

	const startOffset = perPage === -1 ? 0 : ( page - 1 ) * perPage;
} );
