import triggerFetch from '@wordpress/api-fetch';
import {addQueryArgs} from '@wordpress/url';

/**
 * Dispatched a control action for triggering an api fetch call with no parsing.
 * Typically this would be used in scenarios where headers are needed.
 *
 * @param {string} endpoint  The path for the request.
 * @param {string} query  The path for the request.
 *
 * @return {Object} The control action descriptor.
 */
export const fetchFromAPI = (endpoint, query) => {
	return {
		type: 'FETCH_FROM_API',
		endpoint,
		query
	};
};

export const bulkAction = (endpoint, action, ids) => {
	return {
		type: 'TABLE_BULK_ACTION',
		endpoint,
		action,
		ids
	};
};

export const controls = {
	FETCH_FROM_API(action) {
		const queryString = addQueryArgs('', action.query);
		const path = action.endpoint + queryString;
		return new Promise((resolve, reject) => {
			triggerFetch({path, parse: false})
				.then(response => {
					response.json().then(items => {
						resolve({items, headers: response.headers});
					});
				})
				.catch(error => {
					reject(error);
				});
		});
	},
	TABLE_BULK_ACTION(action){

	}
};
