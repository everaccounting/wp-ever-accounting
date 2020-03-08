/**
 * External dependencies
 */

/**
 * Internal dependencies
 */
import { getItems, updateItem, bulkAction } from 'lib/store';
import { accountingApi } from 'lib/api';

import {
	TAXRATES_LOADING,
	TAXRATES_LOADED,
	TAXRATES_FAILED,
	TAXRATES_ITEM_SAVING,
	TAXRATES_ITEM_FAILED,
	TAXRATES_ITEM_SAVED,
	TAXRATES_SET_SELECTED,
	TAXRATES_SET_ALL_SELECTED,
	TAXRATES_ITEM_ADDED,
} from './type';

const STATUS_TAXRATES_ITEM = {
	store: 'taxrates',
	saving: TAXRATES_ITEM_SAVING,
	saved: TAXRATES_ITEM_SAVED,
	failed: TAXRATES_ITEM_FAILED,
	order: 'name',
};
const STATUS_TAXRATE = {
	store: 'taxrates',
	loading: TAXRATES_LOADING,
	loaded: TAXRATES_LOADED,
	failed: TAXRATES_FAILED,
	order: 'name',
};
export const setCreateItem = item => ({ type: TAXRATES_ITEM_ADDED, item });
export const setUpdateItem = (id, item) => (dispatch, getState) =>
	updateItem(accountingApi.taxrates.update, id, item, STATUS_TAXRATES_ITEM, dispatch, getState().taxrates);
export const setGetItems = args => (dispatch, getState) =>
	getItems(accountingApi.taxrates.list, dispatch, STATUS_TAXRATE, args, getState().taxrates);
export const setOrderBy = (orderby, order) => setGetItems({ orderby, order });
export const setPage = page => setGetItems({ page });
export const setFilter = filterBy => setGetItems({ filterBy, orderby: '', page: 1 });
export const setSearch = search => setGetItems({ search, orderby: '', page: 1 });
export const setSelected = items => ({ type: TAXRATES_SET_SELECTED, items: items.map(parseInt) });
export const setAllSelected = onoff => ({ type: TAXRATES_SET_ALL_SELECTED, onoff });
export const setBulkAction = (action, ids) => (dispatch, getState) =>
	bulkAction(accountingApi.taxrates.bulk, action, ids, STATUS_TAXRATE, dispatch, getState().taxrates);
