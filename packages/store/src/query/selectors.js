import {pickBy, isNumber, isEmpty} from "lodash";
import {PER_PAGE} from "@eaccounting/data";

const removeDefaultQueries = (query, defaults = {}) => {
	if (query.order === defaults.order) {
		delete query.order;
	}
	if (query.orderby === defaults.orderby) {
		delete query.orderby;
	}
	if (query.page === 1) {
		delete query.page;
	}
	if (query.per_page === PER_PAGE) {
		delete query.per_page;
	}
	return query;
};


/**
 * Selector for retrieving the query state for the give context
 * @param state
 * @param context
 * @param defaultQuery
 * @returns {*}
 */
export const getQuery = (state, context) => {
	return typeof state[context] === 'undefined' ? {} : JSON.parse(state[context]);
	// const query = pickBy({...defaultQuery, ...stateContext}, value => isNumber(value) || !isEmpty(value));
};

/**
 * Selector for retrieving a specific query-state for the given context.
 *
 * @param {Object} state    Current state.
 * @param {string} context  Context for the query-state being retrieved.
 * @param {string} queryKey Key for the specific query-state item.
 * @param {*} defaultValue  Default value for the query-state key if it doesn't
 *                          currently exist in state.
 *
 * @return {*} The currently stored value or the defaultValue if not present.
 */
export const getQueryByKey = (state, context, queryKey, defaultValue = {}) => {
	let stateContext = typeof state[context] === 'undefined' ? null : state[context];

	if (stateContext === null) {
		return defaultValue;
	}
	stateContext = JSON.parse(stateContext);
	return typeof stateContext[queryKey] !== 'undefined' ? stateContext[queryKey] : defaultValue;
};

/**
 *
 * @param state
 * @param context
 * @returns {*}
 */
export const getPage = (state, context) => {
	return getQueryByKey(state, context, 'page', 1);
};

/**
 *
 * @param state
 * @param context
 * @returns {*}
 */
export const getSearch = (state, context) => {
	return getQueryByKey(state, context, 'search', '');
};

/**
 *
 * @param state
 * @param context
 * @returns {*}
 */
export const getOrder = (state, context) => {
	return getQueryByKey(state, context, 'order', '');
};

/**
 *
 * @param state
 * @param context
 * @returns {*}
 */
export const getOrderBy = (state, context) => {
	return getQueryByKey(state, context, 'orderby', '');
};
