/* global eAccountingi10n */
import axios from 'axios';
import qs from 'qs';
import { pickBy, isEmpty, isNumber } from 'lodash';

/**
 * Create api instance
 * @param endpoint
 * @param params
 * @param data
 * @param config
 * @returns {AxiosInstance}
 */
export const createRequest = (endpoint, params = {}, data = {}, config = {}) => {
	params._wpnonce = eAccountingi10n.api.WP_API_nonce;
	const { filters = {}, ...paramsProps } = params;
	const query = pickBy({ ...paramsProps, ...filters }, value => isNumber(value) || !isEmpty(value));
	return {
		timeout: 1000,
		baseURL: eAccountingi10n.api && eAccountingi10n.api.WP_API_root ? eAccountingi10n.api.WP_API_root : '/wp-json/',
		url: 'ea/v1/' + endpoint + (endpoint.indexOf('?') === -1 ? '?' : '&') + qs.stringify(query),
		method: 'GET',
		credentials: 'same-origin',
		data,
		headers: {
			'X-WP-Nonce': eAccountingi10n.api.WP_API_nonce,
			'x-wp-nonce': eAccountingi10n.api.WP_API_nonce,
			'Content-Type': 'application/json; charset=utf-8',
		},
		...config,
	};
};

/**
 * Prepare get request
 * @param endpoint
 * @param params
 * @returns {AxiosInstance}
 */
export const getApiRequest = (endpoint, params) => {
	return createRequest(endpoint, params, {}, { method: 'GET' });
};

/**
 * prepare post request
 * @param endpoint
 * @param data
 * @param params
 * @returns {AxiosInstance}
 */
export const postApiRequest = (endpoint, data = {}, params = {}) => {
	return createRequest(endpoint, params, data, { method: 'POST' });
};

/**
 * Delete request
 *
 * @param endpoint
 * @param params
 * @param data
 * @returns {AxiosInstance}
 */
export const apiDeleteRequest = (endpoint, params = {}, data = {}) => {
	return createRequest(endpoint, params, data, { method: 'DELETE' });
};

/**
 * Upload request
 * @param endpoint
 * @param file
 * @param params
 */
export const apiUploadRequest = (endpoint, file, params = {}) => {
	const request = createRequest(endpoint, params, data, { method: 'POST' });
	request.headers['Content-Type'] = 'multipart/form-data';
	const data = new FormData();
	data.append('file', file);
};

/**
 * get error message
 *
 * @param response
 * @returns {string|*}
 */
const getErrorMessage = response => {
	if (response.data && response.data.message) {
		return response.data.message;
	}

	if (response.statusText) {
		return response.statusText;
	}

	return 'No data or status object returned in request';
};

/**
 * get error code
 *
 * @param response
 * @returns {string}
 */
const getErrorCode = response => {
	if (response.data && response.data.code) {
		return response.data.code;
	}

	if (response.status) {
		return response.status;
	}

	return 'unknown';
};

/**
 * Main api request wrapper;
 * @param request
 * @returns {Promise<AxiosResponse<T>>}
 */
export const apiRequest = request => {
	axios.interceptors.response.use(
		function(response) {
			response.total =
				response.headers && response.headers['x-wp-total'] ? parseInt(response.headers['x-wp-total'], 10) : undefined;
			return response;
		},
		function(error) {
			const response = error.response;
			error.message = getErrorMessage(response);
			error.code = getErrorCode(response);

			return Promise.reject(error);
		}
	);
	return axios.request(request);
};

/**
 *
 * @type {{invoices: {get: (function(*, *=): AxiosInstance), create: (function(*=): AxiosInstance), update: (function(*, *=): AxiosInstance), list: (function(*=): AxiosInstance), bulk: (function(*, *=, *=): AxiosInstance)}, taxrates: {get: (function(*, *=): AxiosInstance), create: (function(*=): AxiosInstance), update: (function(*, *=): AxiosInstance), list: (function(*=): AxiosInstance), bulk: (function(*, *=, *=): AxiosInstance)}, payments: {get: (function(*, *=): AxiosInstance), create: (function(*=): AxiosInstance), update: (function(*, *=): AxiosInstance), list: (function(*=): AxiosInstance), bulk: (function(*, *=, *=): AxiosInstance)}, bills: {get: (function(*, *=): AxiosInstance), create: (function(*=): AxiosInstance), update: (function(*, *=): AxiosInstance), list: (function(*=): AxiosInstance), bulk: (function(*, *=, *=): AxiosInstance)}, accounts: {get: (function(*, *=): AxiosInstance), create: (function(*=): AxiosInstance), update: (function(*, *=): AxiosInstance), list: (function(*=): AxiosInstance), bulk: (function(*, *=, *=): AxiosInstance)}, categories: {get: (function(*, *=): AxiosInstance), create: (function(*=): AxiosInstance), update: (function(*, *=): AxiosInstance), list: (function(*=): AxiosInstance), bulk: (function(*, *=, *=): AxiosInstance)}, transactions: {get: (function(*, *=): AxiosInstance), create: (function(*=): AxiosInstance), update: (function(*, *=): AxiosInstance), list: (function(*=): AxiosInstance), bulk: (function(*, *=, *=): AxiosInstance)}, revenues: {get: (function(*, *=): AxiosInstance), create: (function(*=): AxiosInstance), update: (function(*, *=): AxiosInstance), list: (function(*=): AxiosInstance), bulk: (function(*, *=, *=): AxiosInstance)}, contacts: {get: (function(*, *=): AxiosInstance), create: (function(*=): AxiosInstance), update: (function(*, *=): AxiosInstance), list: (function(*=): AxiosInstance), bulk: (function(*, *=, *=): AxiosInstance)}, currencies: {get: (function(*, *=): AxiosInstance), create: (function(*=): AxiosInstance), update: (function(*, *=): AxiosInstance), list: (function(*=): AxiosInstance), bulk: (function(*, *=, *=): AxiosInstance)}}}
 */
export const accountingApi = {
	accounts: {
		get: (id, data = {}) => getApiRequest('accounts/' + id, data),
		create: data => postApiRequest('accounts/', data),
		update: (id, data) => postApiRequest('accounts/' + id, data),
		list: params => getApiRequest('accounts', params),
		bulk: (action, data, table) => postApiRequest('bulk' + action, data, table),
	},
	bills: {
		get: (id, data = {}) => getApiRequest('bills/' + id, data),
		create: data => postApiRequest('bills/', data),
		update: (id, data) => postApiRequest('bills/' + id, data),
		list: params => getApiRequest('bills', params),
		bulk: (action, data, table) => postApiRequest('bulk' + action, data, table),
	},
	contacts: {
		get: (id, data = {}) => getApiRequest('contacts/' + id, data),
		create: data => postApiRequest('contacts/', data),
		update: (id, data) => postApiRequest('contacts/' + id, data),
		list: params => getApiRequest('contacts', params),
		bulk: (action, data, table) => postApiRequest('bulk' + action, data, table),
	},
	currencies: {
		get: (id, data = {}) => getApiRequest('currencies/' + id, data),
		create: data => postApiRequest('currencies/', data),
		update: (id, data) => postApiRequest('currencies/' + id, data),
		list: params => getApiRequest('currencies', params),
		bulk: (action, data, table) => postApiRequest('currencies/bulk', data, table),
	},
	categories: {
		get: (id, data = {}) => getApiRequest('categories' + id, data),
		create: data => postApiRequest('categories', data),
		update: (id, data) => postApiRequest('categories/' + id, data),
		list: params => getApiRequest('categories', params),
		bulk: (action, data, table) => postApiRequest('categories/bulk', data, table),
	},
	invoices: {
		get: (id, data = {}) => getApiRequest('invoices/' + id, data),
		create: data => postApiRequest('invoices/', data),
		update: (id, data) => postApiRequest('invoices/' + id, data),
		list: params => getApiRequest('invoices', params),
		bulk: (action, data, table) => postApiRequest('bulk' + action, data, table),
	},
	payments: {
		get: (id, data = {}) => getApiRequest('payments/' + id, data),
		create: data => postApiRequest('payments/', data),
		update: (id, data) => postApiRequest('payments/' + id, data),
		list: params => getApiRequest('payments', params),
		bulk: (action, data, table) => postApiRequest('bulk' + action, data, table),
	},
	revenues: {
		get: (id, data = {}) => getApiRequest('revenues/' + id, data),
		create: data => postApiRequest('revenues/', data),
		update: (id, data) => postApiRequest('revenues/' + id, data),
		list: params => getApiRequest('revenues', params),
		bulk: (action, data, table) => postApiRequest('revenues/bulk', data, table),
	},
	taxrates: {
		get: (id, data = {}) => getApiRequest('taxrates/' + id, data),
		create: data => postApiRequest('taxrates/', data),
		update: (id, data) => postApiRequest('taxrates/' + id, data),
		list: params => getApiRequest('taxrates', params),
		bulk: (action, data, table) => postApiRequest('taxrates/bulk', data, table),
	},
	transactions: {
		get: (id, data = {}) => getApiRequest('transactions/' + id, data),
		create: data => postApiRequest('transactions/', data),
		update: (id, data) => postApiRequest('transactions/' + id, data),
		list: params => getApiRequest('transactions', params),
		bulk: (action, data, table) => postApiRequest('bulk' + action, data, table),
	},
};
