/**
 * WordPress dependencies
 */
import { createRegistryControl } from '@wordpress/data';
import apiFetch from '@wordpress/api-fetch';

export function regularFetch( url ) {
	return {
		type: 'REGULAR_FETCH',
		url,
	};
}

export function getDispatch() {
	return {
		type: 'GET_DISPATCH',
	};
}
export const awaitPromise = function (promise) {
	return {
		type: 'AWAIT_PROMISE',
		promise,
	};
};
/**
 * Dispatched a control action for triggering an api fetch call with no parsing.
 * Typically this would be used in scenarios where headers are needed.
 *
 * @param {string} path  The path for the request.
 *
 * @return {Object} The control action descriptor.
 */
export const fetchFromAPIWithTotal = path => {
	return {
		type: 'FETCH_FROM_API_WITH_TOTAL',
		path,
	};
};

const controls = {
	async REGULAR_FETCH( { url } ) {
		const { data } = await window
			.fetch( url )
			.then( ( res ) => res.json() );
		console.log(data);
		return data;
	},
	FETCH_FROM_API_WITH_TOTAL({ path }) {
		return new Promise((resolve, reject) => {
			apiFetch({ path, parse: false })
				.then(response => {
					response.json().then(items => {
						resolve({ items, total: parseInt(response.headers.get('x-wp-total'), 10) });
					});
				})
				.catch(error => {
					reject(error);
				});
		});
	},
	AWAIT_PROMISE: ( { promise } ) => promise,
	GET_DISPATCH: createRegistryControl( ( { dispatch } ) => () => dispatch ),
};

export default controls;
