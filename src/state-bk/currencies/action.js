/**
 * External dependencies
 */

/**
 * Internal dependencies
 */
import { getItems, updateItem, bulkAction } from 'lib/store';
import { accountingApi } from 'lib/api';

import {
	CURRENCIES_LOADING,
	CURRENCIES_LOADED,
	CURRENCIES_FAILED,
	CURRENCIES_ITEM_SAVING,
	CURRENCIES_ITEM_FAILED,
	CURRENCIES_ITEM_SAVED,
	CURRENCIES_SET_SELECTED,
	CURRENCIES_SET_ALL_SELECTED,
	CURRENCIES_ITEM_ADDED,
} from './type';

const STATUS_CURRENCIES_ITEM = {
	store: 'currencies',
	saving: CURRENCIES_ITEM_SAVING,
	saved: CURRENCIES_ITEM_SAVED,
	failed: CURRENCIES_ITEM_FAILED,
	order: 'name',
};
const STATUS_CURRENCY = {
	store: 'currencies',
	loading: CURRENCIES_LOADING,
	loaded: CURRENCIES_LOADED,
	failed: CURRENCIES_FAILED,
	order: 'name',
};
export const setCreateItem = item => ({ type: CURRENCIES_ITEM_ADDED, item });
export const setUpdateItem = (id, item) => (dispatch, getState) =>
	updateItem(accountingApi.currencies.update, id, item, STATUS_CURRENCIES_ITEM, dispatch, getState().currencies);
export const setGetItems = args => (dispatch, getState) =>
	getItems(accountingApi.currencies.list, dispatch, STATUS_CURRENCY, args, getState().currencies);
export const setOrderBy = (orderby, order) => setGetItems({ orderby, order });
export const setPage = page => setGetItems({ page });
export const setFilter = filterBy => setGetItems({ filterBy, orderby: '', page: 1 });
export const setSearch = search => setGetItems({ search, orderby: '', page: 1 });
export const setSelected = items => ({ type: CURRENCIES_SET_SELECTED, items: items.map(parseInt) });
export const setAllSelected = onoff => ({ type: CURRENCIES_SET_ALL_SELECTED, onoff });
export const setBulkAction = (action, ids) => (dispatch, getState) =>
	bulkAction(accountingApi.currencies.bulk, action, ids, STATUS_CURRENCY, dispatch, getState().currencies);
