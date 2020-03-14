import {ACTION_TYPES as types} from './action-types';
import {TABLE_STORE} from "./index";
import {apiFetch} from '@wordpress/data-controls';
import {fetchFromAPI} from './controls';
import {mergeWithTable, removeDefaults} from './utils';
import {__} from "@wordpress/i18n";

export const receiveResponse = (items = [], headers = {}, table = {}) => {
	return {
		type: types.TABLE_ITEMS_LOADED,
		items,
		headers,
		table
	}
};

export const receiveError = (endpoint, query, error) => {
	return {
		type: types.TABLE_FAILED,
		endpoint,
		query,
		error
	}
};

export const setLoading = () => {
	return {
		type: types.TABLE_LOADING
	}
};

export const setSelected = (id) => {
	return {
		type: types.TABLE_ITEM_SELECTED,
		id
	}
};

export const setAllSelected = (onoff) => {
	console.log(onoff);
	return {
		type: types.TABLE_ALL_SELECTED,
		onoff
	}
};

export function* setBulkAction(endpoint, action, ids) {
	const store = yield TABLE_STORE.getState();
	const {table} = store;
	const data = {
		items: ids ? [ids] : table.selected,
		action,
	};
	if (action === 'delete' && !confirm(__('Are you sure you want to delete the selected items?'))) {
		return false;
	}
	yield apiFetch({
		path: endpoint + '/bulk',
		method: 'POST',
		data,
		cache: 'no-store',
	});

	yield fetchItems()
}


export function* fetchItems(endpoint, query, reduxer = s => s) {
	const store = yield TABLE_STORE.getState();
	const {table = {}} = store;
	const mergedTable = reduxer(mergeWithTable(table, query));
	const params = removeDefaults({...table, ...query});
	try {
		yield setLoading();
		const response = yield fetchFromAPI(endpoint, params);
		const {items = [], headers = {}} = response;
		yield receiveResponse(items, headers, mergedTable);
	} catch (error) {
		yield receiveError(endpoint, params, error);
	}
}


