/* global eAccountingi10n */
import {getPageUrl} from 'lib/wordpress-url';
import {xor, find} from "lodash";

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
 * is equal
 *
 * @param a
 * @param b
 * @returns {boolean}
 */
function isEqual(a, b) {
	for (const name in a) {
		if (a [name] !== b[name]) {
			return false;
		}
	}

	return true;
}

/**
 * Remove default properties
 * @param table
 * @param defaultOrder
 * @returns {*}
 */
export const removeDefaults = (table, defaultOrder) => {
	if (table.order === 'desc') {
		delete table.order;
	}

	if (table.orderby === defaultOrder) {
		delete table.orderby;
	}

	if (table.page === 1) {
		delete table.page;
	}

	if (table.per_page === parseInt(eAccountingi10n.per_page, 10)) {
		delete table.per_page;
	}

	if (table.filters === '' || table.filters === {}) {
		delete table.filters;
	}

	if (parseInt(eAccountingi10n.per_page, 10) !== 20) {
		table.per_page = parseInt(eAccountingi10n.per_page, 10);
	}

	delete table.selected;

	return table;
};

/**
 * Get default table
 * @param allowedOrder
 * @param allowedFilter
 * @param defaultOrder
 * @returns {{per_page: (number), orderby: (*), page: (number), filterBy: (*), selected: [], order: string}}
 */
export const getDefaultTable = (allowedOrder = [], allowedFilter = [], defaultOrder = 'id') => {
	const query = getPageUrl();
	const defaults = {
		orderby: defaultOrder,
		order: 'desc',
		page: 1,
		per_page: parseInt(eAccountingi10n.per_page, 10),
		selected: [],
		filters: {},
	};

	return {
		...defaults,
		orderby: query.orderby && allowedOrder.indexOf(query.orderby) !== -1 ? query.orderby : defaults.orderby,
		order: query.order && query.order === 'asc' ? 'asc' : defaults.order,
		page: query.offset && parseInt(query.offset, 10) > 0 ? parseInt(query.offset, 10) : defaults.page,
		per_page: eAccountingi10n.per_page ? parseInt(eAccountingi10n.per_page, 10) : defaults.per_page,
		filters: query.filters ? pickByKeys(query.filters, allowedFilter) : defaults.filters,
	};
};


/**
 * Set table
 * @param state
 * @param action
 * @returns {*}
 */
export const setTable = (state, action) => (action.meta.table ? {...state.table, ...action.meta.table} : state.table);


/**
 * Set total from api response
 * @param state
 * @param action
 * @returns {number}
 */
export const setTotal = (state, action) => {
	return !isNaN(action.total) ? action.total : state.total;
};

/**
 * Set item saving
 * @param state
 * @param action
 * @returns {*[]}
 */
export const setSaving = (state, action) => [...state.saving, ...action.meta.saving];

/**
 * Remove from saving
 * @param state
 * @param action
 * @returns {*}
 */
export const removeSaving = (state, action) => state.saving.filter(item => action.saving.indexOf(item) === -1);


/**
 * Clear selected values
 * @param state
 * @returns {any}
 */
export const clearSelected = state => {
	return Object.assign({}, state, {selected: []});
};


/**
 * Set all selected
 * @param table
 * @param rows
 * @param onoff
 * @returns {{selected: Array}}
 */
export const setTableAllSelected = (table, rows, onoff) => ({
	...table,
	selected: onoff ? rows.map(item => item.id) : [],
});


/**
 * Toggle item
 * @param table
 * @param ids
 * @returns {{selected: *}}
 */
export const setTableSelected = (table, ids) => ({...table, selected: xor(table.selected, ids)});

/**
 * Set updated items
 * @param rows
 * @param action
 * @returns {*}
 */
export const setUpdatedItem = (rows, action) => rows.map(row => (parseInt(row.id, 10) === parseInt(action.item.id, 10) ? {...row, ...action.item} : row));

/**
 * Set deleted Item
 * @param rows
 * @param item
 * @returns {*}
 */
export const setDeletedItem = (rows, item) => rows.filter(obj => obj.id !== item.id);

/**
 *
 * @param options
 * @param value
 * @returns {*[]}
 */
export const getSelectedOptions = (options = [], value = []) => {
	return options.filter((filter) => {
		return value.includes(filter.value) === true;
	})
};

export const getSelectedOption = (options = [], selected = '', initial = '') => {
	return find(options, {value: (selected || initial)});
};
