/**
 * External dependencies
 */
import { has, setWith, clone, forOwn } from 'lodash';

/**
 * Utility for returning whether the given path exists in the state.
 *
 * @param {Object} state The state being checked
 * @param {Array}  path  The path to check
 *
 * @return {boolean} True means this exists in the state.
 */
export const hasInState = (state, path) => {
	return has(state, path);
};
/**
 * Utility for updating state and only cloning objects in the path that changed.
 *
 * @param {Object} state The state being updated
 * @param {Array}  path  The path being updated
 * @param {*}      value The value to update for the path
 *
 * @return {Object} The new state
 */
export const updateState = (state, path, value) => {
	return setWith(clone(state), path, value, clone);
};

export const replaceItem = (collection, item) => {
	window.collection = collection;
	return forOwn(collection, (val, key) => {
		// val.map(i => i.id === item.id ? item : i);
	});
};
