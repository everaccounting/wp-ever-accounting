/**
 * Internal dependencies
 */
import { ACTION_TYPES as types } from './action-types';

/**
 * Action creator for setting a single query-state value for a given context.
 *
 * @param {string} context  Context for query state being stored.
 * @param {string} queryKey Key for query item.
 * @param {*}      value    The value for the query item.
 *
 * @return {Object} The action object.
 */
export const setQuery = (context, queryKey, value) => {
	return {
		type: types.SET_QUERY,
		context,
		queryKey,
		value,
	};
};

/**
 * Action creator for setting page number for a given context.
 *
 * @param {string} context  Context for query state being stored.
 * @param {*}      value    The value for the query item.
 *
 * @return {Object} The action object.
 */
export const setPage = (context, value) => {
	return {
		type: types.SET_QUERY,
		context,
		queryKey: 'page',
		value,
	};
};

/**
 * Action creator for setting search for a given context.
 *
 * @param {string} context  Context for query state being stored.
 * @param {*}      value    The value for the query item.
 *
 * @return {Object} The action object.
 */
export const setSearch = (context, value) => {
	return {
		type: types.SET_QUERY,
		context,
		queryKey: 'search',
		value,
	};
};

/**
 * Action creator for setting orderby for a given context.
 *
 * @param {string} context  Context for query state being stored.
 * @param {*}      value    The value for the query item.
 *
 * @return {Object} The action object.
 */
export const setOrderBy = (context, value) => {
	return {
		type: types.SET_QUERY,
		context,
		queryKey: 'orderby',
		value,
	};
};

/**
 * Action creator for setting order for a given context.
 *
 * @param {string} context  Context for query state being stored.
 * @param {*}      value    The value for the query item.
 *
 * @return {Object} The action object.
 */
export const setOrder = (context, value) => {
	return {
		type: types.SET_QUERY,
		context,
		queryKey: 'order',
		value,
	};
};

/**
 * Action creator for setting filter for a given context.
 *
 * @param {string} context  Context for query state being stored.
 * @param {Object}      filter    The filter for the query item.
 * @param {Object}      query    Existing query
 *
 * @return {Object} The action object.
 */
export const setFilter = (context, filter, query = {}) => {
	return {
		type: types.SET_CONTEXT_QUERY,
		context,
		value: Object.assign({}, { ...query, page: 1 }, filter),
	};
};

/**
 * Action creator for setting query-state for a given context.
 *
 * @param {string} context Context for query state being stored.
 * @param {*}      value   Query state being stored for the given context.
 *
 * @return {Object} The action object.
 */
export const setContextQuery = (context, value) => {
	return {
		type: types.SET_CONTEXT_QUERY,
		context,
		value,
	};
};

/**
 * Action creator for resetting query-state for a given context.
 *
 * @param context Context for query state being stored.
 * @return {{context: *, type: string}}
 */
export const resetQuery = context => {
	return {
		type: types.RESET_CONTEXT_QUERY,
		context,
	};
};
