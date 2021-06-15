/**
 * External dependencies
 */
import { controls as dataControls } from '@wordpress/data-controls';

import apiFetch from '@wordpress/api-fetch';
import {dispatch as dispatchData, select as selectData, subscribe} from "@wordpress/data";
/**
 * Returns the action object for a fetch control.
 *
 * @param {Object} request
 * @return {{type: string, request: Object}} An action object
 */
export function fetch(request) {
	return {
		type: 'FETCH_FROM_API',
		request,
	};
}
/**
 * Dispatched a control action for triggering an api fetch call with no parsing.
 * Typically this would be used in scenarios where headers are needed.
 *
 * @param {object} options  The path for the request.
 *
 * @return {Object} The control action descriptor.
 */
export const fetchWithHeaders = ( options ) => {
	return {
		type: 'FETCH_WITH_HEADERS',
		options,
	};
};

/**
 * Returns the action object for a select control.
 *
 * @param {string} reducerKey
 * @param {string} selectorName
 * @param {*[]} args
 * @return {{type: string, reducerKey: string, selectorName: string, args: *[]}}
 * Returns an action object.
 */
export function select(reducerKey, selectorName, ...args) {
	return {
		type: 'SELECT',
		reducerKey,
		selectorName,
		args,
	};
}

/**
 * Returns the action object for resolving a selector that has a resolver.
 *
 * @param {string} reducerKey
 * @param {string} selectorName
 * @param {Array} args
 * @return {Object} An action object.
 */
export function resolveSelect(reducerKey, selectorName, ...args) {
	return {
		type: 'RESOLVE_SELECT',
		reducerKey,
		selectorName,
		args,
	};
}

/**
 * Returns the action object for a dispatch control.
 *
 * @param {string} reducerKey
 * @param {string} dispatchName
 * @param {*[]} args
 * @return {{type: string, reducerKey: string, dispatchName: string, args: *[]}}
 * An action object
 */
export function dispatch(reducerKey, dispatchName, ...args) {
	return {
		type: 'DISPATCH',
		reducerKey,
		dispatchName,
		args,
	};
}

/**
 * Returns the action object for a resolve dispatch control
 *
 * @param {string} reducerKey
 * @param {string} dispatchName
 * @param {Array} args
 * @return {Object} The action object.
 */
export function resolveDispatch(reducerKey, dispatchName, ...args) {
	return {
		type: 'RESOLVE_DISPATCH',
		reducerKey,
		dispatchName,
		args,
	};
}

const controls = {
	...dataControls,
	FETCH_FROM_API({ request }) {
		return apiFetch(request);
	},
	FETCH_WITH_HEADERS( { options } ) {
		return new Promise((resolve, reject) => {
			apiFetch({ ...options, parse: false })
				.then((response) => {
					response.json().then((data) => {
						resolve({
							data,
							headers: response.headers,
						});
					});
				})
				.catch((error) => {
					reject(error);
				});
		});
	},
	SELECT({ reducerKey, selectorName, args }) {
		return selectData(reducerKey)[selectorName](...args);
	},
	DISPATCH({ reducerKey, dispatchName, args }) {
		return dispatchData(reducerKey)[dispatchName](...args);
	},
	RESOLVE_SELECT({ reducerKey, selectorName, args }) {
		return new Promise((resolve) => {
			const hasFinished = () =>
				selectData('core/data').hasFinishedResolution(
					reducerKey,
					selectorName,
					args
				);

			const getResult = () =>
				selectData(reducerKey)[selectorName].apply(null, args);

			// trigger the selector (to trigger the resolver)
			const result = getResult();
			if (hasFinished()) {
				return resolve(result);
			}

			const unsubscribe = subscribe(() => {
				if (hasFinished()) {
					unsubscribe();
					resolve(getResult());
				}
			});
		});
	},
	async RESOLVE_DISPATCH({ reducerKey, dispatchName, args }) {
		return await dispatchData(reducerKey)[dispatchName](...args);
	},
};

export default controls;
