import {hasInState} from "./utils";

export const getRows = (state) => {
	if (hasInState(state, ['items'])) {
		return state['items'];
	}

	return [];
};


/**
 * Get total
 * @param state
 * @returns {number|*}
 */
export const getTotal = (state) => {
	if (hasInState(state, ['total'])) {
		return state['total'];
	}
	return 0
};


/**
 * Get total
 * @param state
 * @returns {number|*}
 */
export const getTable = (state) => {
	if (hasInState(state, ['table'])) {
		return state['table'];
	}
	return {};
};


/**
 * Get total
 * @param state
 * @returns {number|*}
 */
export const getStatus = (state) => {
	if (hasInState(state, ['status'])) {
		return state['status'];
	}
	return "STATUS_IN_PROGRESS";
};
