
/**
 * Provided via the data passed along by the server.
 * This data has to do with any paths/route information passed along from the
 * server.
 *
 * @type { {} }
 */
const data = window.eaccountingi10n || {};

/**
 * The base url for the site this js is loaded on.
 * eg. 'https://mysite.com/'
 *
 * @type { string }
 */
export const SITE_URL = data.site_url || '';

/**
 * The base admin url for the site this js is loaded on.
 * eg. 'https://mysite.com/wp-admin/
 *
 * @type { string }
 */
export const ADMIN_URL = data.admin_url || '';

/**
 * The asset url of the plugin.
 * eg. 'https://mysite.com/wp-content/plugins/wp-ever-accounting'
 *
 * @type { string }
 */
export const ASSET_URL = data.asset_url || '';
/**
 * The plugin url of the plugin.
 * eg. 'https://mysite.com/wp-content/plugins/wp-ever-accounting'
 *
 * @type { string }
 */
export const PLUGIN_URL = data.plugin_url || '';
