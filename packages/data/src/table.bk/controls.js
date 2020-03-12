import triggerFetch from '@wordpress/api-fetch';

/**
 * Dispatched a control action for triggering an api fetch call with no parsing.
 * Typically this would be used in scenarios where headers are needed.
 *
 * @param {string} path  The path for the request.
 *
 * @return {Object} The control action descriptor.
 */
export const apiFetchWithHeaders = path => {
	return {
		type: 'API_FETCH_WITH_HEADERS',
		path,
	};
};

/**
 * Default export for registering the controls with the store.
 *
 * @return {Object} An object with the controls to register with the store on
 *                  the controls property of the registration object.
 */
export const controls = {
	API_FETCH_WITH_HEADERS({path}) {
		return new Promise((resolve, reject) => {
			triggerFetch({path, parse: false})
				.then(response => {
					response.json().then(items => {
						//const total = parseInt(response.headers.get('x-wp-total'), 10);
						resolve({items, headers: response.headers});
					});
				})
				.catch(error => {
					reject(error);
				});
		});
	},

	FETCH_FROM_API({action}) {
		console.group('FETCH_FROM_API');
		console.log(action)
		console.groupEnd();
	}
};
