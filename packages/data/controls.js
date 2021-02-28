/**
 * WordPress dependencies
 */
import { createRegistryControl } from '@wordpress/data';
import { select as selectData, dispatch as dispatchData, subscribe } from '@wordpress/data';

export function regularFetch( url ) {
	return {
		type: 'REGULAR_FETCH',
		url,
	};
}

export const fetchWithTotal = (options) => {
	return {
		type: 'FETCH_FROM_API_WITH_TOTAL',
		options,
	};
};


export function getDispatch() {
	return {
		type: 'GET_DISPATCH',
	};
}

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

export const awaitPromise = function (promise) {
	return {
		type: 'AWAIT_PROMISE',
		promise,
	};
};

const controls = {
	async REGULAR_FETCH( { url } ) {
		const { data } = await window
			.fetch( url )
			.then( ( res ) => res.json() );

		return data;
	},
	AWAIT_PROMISE: ( { promise } ) => promise,
	GET_DISPATCH: createRegistryControl( ( { dispatch } ) => () => dispatch ),
	SELECT({ reducerKey, selectorName, args }) {
		return selectData(reducerKey)[selectorName](...args);
	},
	DISPATCH({ reducerKey, dispatchName, args }) {
		return dispatchData(reducerKey)[dispatchName](...args);
	},
	async RESOLVE_DISPATCH({ reducerKey, dispatchName, args }) {
		return await dispatchData(reducerKey)[dispatchName](...args);
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
	RESOLVE_SELECT({ reducerKey, selectorName, args }) {
		return new Promise(resolve => {
			const hasFinished = () => selectData('core/data').hasFinishedResolution(reducerKey, selectorName, args);
			const getResult = () => selectData(reducerKey)[selectorName].apply(null, args);

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
};

export default controls;
