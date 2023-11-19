export const data = window.eacAssetData || {};

/**
 * Retrieves a data value from the data state.
 * @param {string}                  name     The name of the data to retrieve.
 * @param {string | Object | Array} fallback The fallback value to use if the data is not in the state.
 * @param {Function}                filter   A callback for filtering the value before it's returned.
 * @return {*}  The value present in the data state for the given
 *                   name.
 */
export const getData = ( name, fallback = false, filter = ( val ) => val ) => {
	const value = data.hasOwnProperty( name ) ? data[ name ] : fallback;

	return filter( value, fallback );
};
export const _DEFAULT_CURRENCY = {
	code: 'USD',
	name: 'US Dollar',
	precision: 2,
	symbol: '$',
	position: 'before',
	thousand_separator: ',',
	decimal_separator: '.',
	exchange_rate: 1,
};
export const ADMIN_URL = getData( 'adminUrl' );
export const COUNTRIES = getData( 'countries' );
export const CURRENCY = getData( 'currency' );
export const LOCALE = getData( 'locale' );
export const SITE_TITLE = getData( 'siteTitle' );
