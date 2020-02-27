/* global eAccountingi10n */
import {getPageUrl} from 'lib/wordpress-url';

/**
 * Merge table data with params
 * @param table
 * @param params
 * @returns {any}
 */
export const mergeWithTable = (table, params) => {
	const tableParams = ['orderby', 'order', 'page', 'per_page', 'filterBy'];
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

	if (table.filterBy === '' || table.filterBy === {}) {
		delete table.filterBy;
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
		filterBy: {},
	};

	return {
		...defaults,
		orderby: query.orderby && allowedOrder.indexOf(query.orderby) !== -1 ? query.orderby : defaults.orderby,
		order: query.order && query.order === 'asc' ? 'asc' : defaults.order,
		page: query.offset && parseInt(query.offset, 10) > 0 ? parseInt(query.offset, 10) : defaults.page,
		per_page: eAccountingi10n.per_page ? parseInt(eAccountingi10n.per_page, 10) : defaults.per_page,
		filterBy: query.filterby ? pickByKeys(query.filterby, allowedFilter) : defaults.filterBy,
	};
};


/**
 * Set table
 * @param state
 * @param action
 * @returns {*}
 */
export const setTable = (state, action) => (action.table ? {...state.table, ...action.table} : state.table);



/**
 * Set total from api response
 * @param state
 * @param action
 * @returns {number}
 */
export const setTotal = (state, action) => {
	return action.total || state.total;
};

/**
 * Clear selected values
 * @param state
 * @returns {any}
 */
export const clearSelected = state => {
	return Object.assign( {}, state, { selected: [] } );
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
 * Setup selected items
 *
 * @param table
 * @param newItems
 * @returns {{selected: (Array|*[])}}
 */
export const setTableSelected = (table, newItems) => ({ ...table, selected: toggleSelected(table.selected, newItems) });
