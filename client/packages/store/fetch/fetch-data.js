/**
 * WordPress dependencies
 */
import apiFetch from '@wordpress/api-fetch';
import {
	addQueryArgs,
	prependHTTP,
	isURL,
	getProtocol,
	isValidProtocol,
} from '@wordpress/url';

/**
 * A simple in-memory cache for requests.
 * This avoids repeat HTTP requests which may be beneficial
 * for those wishing to preserve low-bandwidth.
 */
const CACHE = new Map();

const fetchData = async (url, options = {}) => {
	// url maybe a relative path or an absolute URL.
	// If it is a relative path, we will prepend the rest api base URL.
	// If it is an absolute URL, we will use it as is.
	// Test for "http" based URL as it is possible for valid
	// yet unusable URLs such as `tel:123456` to be passed.
	const protocol = getProtocol(url);
	// check if this is a relative path
	if (
		!protocol ||
		!isValidProtocol(protocol) ||
		!protocol.startsWith('http') ||
		!/^https?:\/\/[^\/\s]/i.test(url)
	) {
		url = addQueryArgs(url);
	}
	const baseURL = apiFetch.getURL();
};
