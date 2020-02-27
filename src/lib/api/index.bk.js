/* global eAccountingi10n */
/**
 *
 * @format
 */

/**
 * Internal dependencies
 */
import querystring from 'qs';

const removeEmpty = item =>
	Object.keys(item)
		.filter(key => item[key] && key !== 'displaySelected' && key !== 'displayType')
		.reduce((newObj, key) => {
			newObj[key] = item[key];
			return newObj;
		}, {});

export const getApiUrl = () => eAccountingi10n.api && eAccountingi10n.api.WP_API_root ? eAccountingi10n.api.WP_API_root : '/wp-json/';
export const setApiUrl = url => eAccountingi10n.api.WP_API_root = url;
export const getApiNonce = () => eAccountingi10n.api.WP_API_nonce;
const setApiNonce = nonce => eAccountingi10n.api.WP_API_nonce = nonce;


const getRequestUrl = (path, params = {}) => {
	const base = getApiUrl() + 'ea/v1/' + path + '/';
	// Some servers dont pass the X-WP-Nonce through to PHP
	params._wpnonce = getApiNonce();

	if (params && Object.keys(params).length > 0) {
		params = removeEmpty(params);

		if (Object.keys(params).length > 0) {
			return base + (getApiUrl().indexOf('?') === -1 ? '?' : '&') + querystring.stringify(params);
		}
	}

	return base;
};

const apiHeaders = () => {
	return new Headers({
		'X-WP-Nonce': eAccountingi10n.api.WP_API_nonce,
		'Content-Type': 'application/json; charset=utf-8',
	});
};

const apiRequest = url => ({
	url,
	headers: apiHeaders(url),
	credentials: 'same-origin',
});

const getApiRequest = (path, params = {}) => ({
	headers: apiRequest(),
	...apiRequest(getRequestUrl(path, params)),
	method: 'get',
});

const uploadApiRequest = ( path, file ) => {
	const request = { headers: postApiheaders(), ...apiRequest( getAccountingUrl( path ) ), method: 'post' };

	request.headers.delete( 'Content-Type' );
	request.body = new FormData();
	request.body.append( 'file', file );

	return request;
};

const postApiRequest = (path, params = {}, query = {}) => {
	const request = {...apiRequest(getRequestUrl(path, query)), method: 'post', params};

	request.body = '{}';
	if (Object.keys(params).length > 0) {
		request.body = JSON.stringify(params);
	}

	return request;
};

const deleteApiRequest = (path, params) => {
	const query = {...params};
	const body = {};

	if (params && params.items) {
		body.items = params.items;
		delete query.items;
	}

	return {
		...apiRequest(getRequestUrl(path, query)),
		method: 'delete',
		body: body.items ? JSON.stringify(body) : '{}',
	};
};


const getAction = request =>
	request.url.replace(getApiUrl(), '').replace(/[\?&]_wpnonce=[a-f0-9]*/, '') +
	' ' +
	request.method.toUpperCase();

const getErrorMessage = json => {
	if (json === 0) {
		return 'Admin AJAX returned 0';
	}

	if (json.message) {
		return json.message;
	}

	return 'Unknown error ' + json;
};

const getErrorCode = json => {
	if (json.error_code) {
		return json.error_code;
	}

	if (json.data && json.data.error_code) {
		return json.data.error_code;
	}

	if (json === 0) {
		return 'admin-ajax';
	}

	if (json.code) {
		return json.code;
	}

	return 'unknown';
};


export const getApi = request => {
	request.action = getAction(request);
	let headers = {};
	return fetch(request.url, request)
		.then(data => {

			if (!data || !data.status) {
				throw {message: 'No data or status object returned in request', code: 0};
			}

			if (data.status && data.statusText !== undefined) {
				request.status = data.status;
				request.statusText = data.statusText;
			}

			if (data.headers.get('x-wp-nonce')) {
				setApiNonce(data.headers.get('x-wp-nonce'));
			}

			headers = data.headers;
			return data.text();
		})
		.then(text => {
			request.raw = text;

			try {
				const data = JSON.parse(text.replace(/\ufeff/, ''));
				if (request.status && ![200, 201, 202, 204].includes(request.status)) {
					throw {
						message: getErrorMessage(data),
						code: getErrorCode(data),
						request,
						data: data.data ? data.data : null,
					};
				}

				if (data === 0) {
					throw {message: 'Failed to get data', code: 'json-zero'};
				}

				const response = {
					items:data,
					total:undefined,
					headers
				};

				let total = parseInt(headers.get('x-wp-total'), 10);
				if(!isNaN(total)){
					response.total = total;
				}
				return response;

				// return {
				// 	items:data,
				// 	total: parseInt(headers.get('x-wp-total'), 10),
				// 	total_page: parseInt(headers.get('x-wp-totalpages'), 10),
				// 	headers
				// };

			} catch (error) {
				error.request = request;
				error.code = error.code || error.name;
				throw error;
			}
		});
};
