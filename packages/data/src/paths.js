/**
 * Internal imports
 */
/**
 * Internal dependencies
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
 *
 * @type { string }
 */
export const SITE_URL = paths.site_url || '';

/**
 * The namespace of the plugin where all the request will forwarded
 * eg. '/ea/v1'
 *
 * @type { string }
 */
export const NAMESPACE = paths.namespace || '';

/**
 * The base admin url for the site this js is loaded on.
 * eg. 'https://mysite.com/wp-admin/
 *
 * @type { string }
 */
export const ADMIN_URL = paths.admin_url || '';

/**
 * The asset url of the plugin.
 * eg. 'https://mysite.com/wp-content/plugins/wp-ever-accounting'
 *
 * @type { string }
 */
export const ASSET_URL = paths.asset_url || '';
