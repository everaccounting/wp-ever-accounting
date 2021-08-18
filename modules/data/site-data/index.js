/**
 * This will hold arbitrary data assigned by the Assets Registry.
 *
 * @type {{}}
 */
export const localizedData = window.eaccountingApp || {};

/**
 * The api name space for the app.
 * eg. '/ea/v1'
 *
 * @type { string }
 */
export const API_NAMESPACE = localizedData.api_root || '/ea/v1';

/**
 * The base url for the site this js is loaded on.
 * eg. 'https://mysite.com/'
 *
 * @type { string }
 */
export const SITE_URL = localizedData.site_url || '';

/**
 * The base admin url for the site this js is loaded on.
 * eg. 'https://mysite.com/wp-admin/
 *
 * @type { string }
 */
export const ADMIN_URL = localizedData.admin_url || '';

/**
 * The base dist url for the site this js is loaded on.
 * eg. 'https://mysite.com/wp-content/plugins/wp-ever-accounting/dist
 *
 * @type { string }
 */
export const DIST_URL = localizedData.dist_url || '';

/**
 * The maximum resource to load per page.
 * eg. 20
 *
 * @type { number }
 */
export const PER_PAGE = localizedData.per_page || 20;

/**
 * Export countries list.
 *
 * @type {Array}
 */
export const COUNTRIES = localizedData.countries || [];

/**
 * Export category types.
 *
 * @type {Array}
 */
export const CATEGORY_TYPES = localizedData.category_types || [];

/**
 * Export payment methods.
 *
 * @type {Array}
 */
export const PAYMENT_METHODS = localizedData.payment_methods || [];

/**
 * Export currency codes.
 *
 * @type {Array}
 */
export const CURRENCY_CODES = localizedData.codes || [];

/**
 * Export transaction types.
 *
 * @type {Array}
 */
export const TRANSACTION_TYPES = localizedData.transaction_types || {};

/**
 * Get default currency.
 *
 * @type {*|{decimal_separator: string, symbol: string, code: string, rate: string, precision: number, name: string, id: string, position: string, thousand_separator: string, enabled: boolean}}
 */
export const DEFAULT_CURRENCY = localizedData.default_currency || {
	code: 'USD',
	decimal_separator: '.',
	enabled: true,
	id: '',
	name: 'US Dollar',
	position: 'before',
	precision: 2,
	rate: '1.0000000',
	symbol: '$',
	thousand_separator: ',',
};

/**
 * Export transaction types.
 *
 * @type {Array}
 */
export const TIME_FORMAT = localizedData.time_format || 'H:i';

/**
 * Export date format.
 *
 * @type {Array}
 */
export const DATE_FORMAT = localizedData.date_format || 'Y-m-d';

/**
 * Get data from localized data
 *
 * @param {string} property Called property
 * @param {any} defaults Default data.
 * @return {any} Returns the requested data.
 */
export const getSiteData = (property, defaults = '') => {
	return localizedData[property] || defaults;
};
