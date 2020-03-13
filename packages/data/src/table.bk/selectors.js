import {hasInState} from "../utils";
import {addQueryArgs} from '@wordpress/url';

export const getItems = (endpoint) =>{

};

/**
 * Get collection
 * @param state
 * @param endpoint
 * @param query
 * @returns {*[]|*}
 */
export const getCollection = (state, endpoint, query = {}) => {
	query = query !== null ? addQueryArgs('', query) : '';

	if (hasInState(state, [endpoint, query])) {
		return state[endpoint][query];
	}

	return [];
};

/**
 * Get total
 * @param state
 * @param endpoint
 * @returns {number|*}
 */
export const getTotal = (state, endpoint) => {
	if (hasInState(state, [endpoint, 'total'])) {
		return parseInt(state[endpoint]['total'], 10);
	}
	return 0
};

/**
 *
 * @param state
 * @returns {*[]|*}
 */
export const getTable = (state) => {
	return state['table']
};

/**
 *
 * @param state
 * @returns {*[]|*}
 */
export const getStatus = (state) => {
	return state['status'] || "STATUS_IN_PROGRESS";
};
