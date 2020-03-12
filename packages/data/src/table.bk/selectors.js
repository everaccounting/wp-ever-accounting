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
		return state[endpoint]['total'];
	}
	return 0
};

/**
 *
 * @param state
 * @param endpoint
 * @returns {*[]|*}
 */
export const getSelected = (state, endpoint) => {
	if (hasInState(state, [endpoint, 'selected'])) {
		return state[endpoint]['selected'];
	}
	return [];
};

/**
 *
 * @param state
 * @param endpoint
 * @returns {*[]|*}
 */
export const getStatus = (state, endpoint) => {
	if (hasInState(state, [endpoint, 'status'])) {
		return state[endpoint]['status'];
	}
	return "IN_PROGRESS";
};
