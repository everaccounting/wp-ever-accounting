/* global eAccountingi10n */
/**
 * Internal dependencies
 */

import {getPageUrl} from 'lib/wordpress-url';

const tableParams = ['orderby', 'order', 'page', 'search', 'per_page', 'filter'];

const removeIfExists = (current, newItems) => {
	const newArray = [];

	for (let x = 0; x < current.length; x++) {
		if (newItems.indexOf(current[x]) === -1) {
			newArray.push(current[x]);
		}
	}

	return newArray;
};

const strOrInt = value => parseInt(value, 10) > 0 || value === '0' ? parseInt(value, 10) : value;

function filterFilters(query, filters) {
	const filteredQuery = {};
	Object.keys(query).map(key => {
		if (filters[key] && Array.isArray(filters[key]) && filters[key].indexOf(strOrInt(query[key])) !== -1) {
			filteredQuery[key] = strOrInt(query[key]);
		} else if (filters[key] && !Array.isArray(filters[key])) {
			filteredQuery[key] = query[key];
		}
	});

	return filteredQuery;
}

export const getDefaultTable = (allowedOrder = [], allowedFilter = [], defaultOrder = '') => {
	const query = getPageUrl();
	const defaults = {
		orderby: defaultOrder,
		order: 'desc',
		page: 1,
		per_page: parseInt(eAccountingi10n.per_page, 10),
		selected: [],
		filter: [],
		search: '',
	};

	return {
		...defaults,
		orderby: query.orderby && allowedOrder.indexOf(query.orderby) !== -1 ? query.orderby : defaults.orderby,
		order: query.order && query.order === 'asc' ? 'asc' : defaults.order,
		page: query.offset && parseInt(query.offset, 10) > 0 ? parseInt(query.offset, 10) : defaults.page,
		per_page: eAccountingi10n.per_page ? parseInt(eAccountingi10n.per_page, 10) : defaults.per_page,
		filter: query.filter ? filterFilters(query.filter, allowedFilter) : defaults.filter,
	};
};

export const mergeWithTable = (state, params) => {
	const newState = Object.assign({}, state);
	const {filter = {}} = state;
	const {filter:paramFilter = {} } = params;
	params = {...params, filter:{...filter, ...paramFilter}};
	for (let x = 0; x < tableParams.length; x++) {
		if (params[tableParams[x]] !== undefined) {
			newState[tableParams[x]] = params[tableParams[x]];
		}
	}
	return newState;
};

export const removeDefaults = (table, defaultOrder) => {
	if (table.order === 'desc') {
		delete table.order;
	}

	if (table.orderby === defaultOrder) {
		delete table.orderby;
	}

	if (table.page === 0) {
		delete table.page;
	}

	if (table.per_page === parseInt(eAccountingi10n.per_page, 10)) {
		delete table.per_page;
	}

	if (parseInt(eAccountingi10n.per_page, 10) !== 20) {
		table.per_page = parseInt(eAccountingi10n.per_page, 10);
	}

	delete table.selected;

	return table;
};

export const clearSelected = state => {
	return Object.assign({}, state, {selected: []});
};

export const setTableSelected = (table, newItems) => ({
	...table,
	selected: removeIfExists(table.selected, newItems).concat(removeIfExists(newItems, table.selected))
});
export const setTableAllSelected = (table, rows, onoff) => ({
	...table,
	selected: onoff ? rows.map(item => parseInt(item.id, 10)) : []
});
export const tableKey = ({filterBy, filter}) => [filterBy, filter].join('-');

export const toFilter = (filter, extra) => {
	const filtered = {};

	filter.map(({value, options}) => {
		filtered[value] = Array.isArray(options) ? options.map(item => item.value) : value;
	});

	return {...filtered, ...extra};
};
