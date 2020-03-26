/**
 * Internal imports
 */
import data from './data';

/**
 * Provided via the data passed along by the server.
 * This data has to do with any paths/route information passed along from the
 * server.
 *
 * @type { {} }
 */
const { paths = {} } = data;

/**
 * The base url for the site this js is loaded on.
 * eg. 'https://mysite.com/'
 * @type { string }
 */
export const SITE_URL = paths.site_url || '';

/**
 * The collections endpoints
 *
 * @type {*|*[]}
 */
export const COLLECTION_ENDPOINTS = paths.collection_endpoints||[];

/**
 * Models
 * @type {*|*[]}
 */
export const MODELS = paths.models||[];

/**
 * The base admin url for the site this js is loaded on.
 * eg. 'https://mysite.com/wp-admin/
 * @type { string }
 */
export const ADMIN_URL = paths.admin_url || '';

/**
 * The asset url of the plugin.
 * eg. 'https://mysite.com/wp-content/plugins/wp-ever-accounting'
 * @type { string }
 */
export const ASSET_URL = paths.asset_url || '';
