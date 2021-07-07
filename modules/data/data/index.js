/**
 * This will hold arbitrary data assigned by the Assets Registry.
 *
 * @type {{}}
 */
export const localizedData = window.eaccountingApp || {};

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
 * Get data from localized.
 *
 * @param {string} property Called property
 * @param {any} defaults Default data.
 * @return {any} Returns the requested data.
 */
export const getLocalizedData = ( property, defaults = '' ) => {
	return localizedData[ property ] || defaults;
};
