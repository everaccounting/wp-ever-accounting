/**
 * External dependencies
 */
import { has,setWith, clone  } from 'lodash';

/**
 * Utility for returning whether the given path exists in the state.
 *
 * @param {Object} state The state being checked
 * @param {Array}  path  The path to check
 *
 * @return {boolean} True means this exists in the state.
 */
export function hasInState(state, path) {
	return has(state, path);
}


/**
 * Utility for updating state and only cloning objects in the path that changed.
 *
 * @param {Object} state The state being updated
 * @param {Array}  path  The path being updated
 * @param {*}      value The value to update for the path
 *
 * @return {Object} The new state
 */
export function updateState(state, path, value) {
	return setWith(clone(state), path, value, clone);
}


/**
 * Merge table data with params
 * @param table
 * @param params
 * @returns {any}
 */
export const mergeWithTable = (table, params) => {
	const tableParams = ['orderby', 'order', 'page', 'per_page', 'filters'];
	const data = Object.assign({}, table);
	for (let x = 0; x < tableParams.length; x++) {
		if (params[tableParams[x]] !== undefined) {
			data[tableParams[x]] = params[tableParams[x]];
		}
	}
	return data;
};


/**
 * Remove default properties
 * @param table
 * @returns {*}
 */
export const removeDefaults = (table) => {
	if (table.order === 'desc') {
		delete table.order;
	}

	if (table.page === 1) {
		delete table.page;
	}

	if (table.per_page === 20) {
		delete table.per_page;
	}

	if (table.filters === '' || table.filters === {}) {
		delete table.filters;
	}

	delete table.selected;

	return table;
};

