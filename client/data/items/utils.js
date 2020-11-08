/**
 * Internal dependencies
 */
import { STORE_NAME } from './constants';

/**
 * Returns items based on a search query.
 *
 * @param  {Object}   select    Instance of @wordpress/select
 * @param  {string}   endpoint  Report API Endpoint
 * @param  {string[]} search    Array of search strings.
 * @return {Object}   Object containing API request information and the matching items.
 */
export function searchItems( select, endpoint, search ) {
	const { getItems, getItemsError, isResolving } = select( STORE_NAME );

	const items = {};
	let isRequesting = false;
	let isError = false;
	search.forEach( ( searchWord ) => {
		const query = {
			search: searchWord,
			per_page: 10,
		};
		const newItems = getItems( endpoint, query );
		newItems.forEach( ( item, id ) => {
			items[ id ] = item;
		} );
		if ( isResolving( 'getItems', [ endpoint, query ] ) ) {
			isRequesting = true;
		}
		if ( getItemsError( endpoint, query ) ) {
			isError = true;
		}
	} );

	return { items, isRequesting, isError };
}
