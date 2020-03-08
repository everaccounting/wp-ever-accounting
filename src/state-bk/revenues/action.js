/**
 * Internal dependencies
 */
import {
	REVENUES_LOADING,
	REVENUES_LOADED,
	REVENUES_FAILED,
	REVENUES_ITEM_SAVING,
	REVENUES_ITEM_FAILED,
	REVENUES_ITEM_SAVED,
	REVENUES_SET_SELECTED,
	REVENUES_SET_ALL_SELECTED,
	REVENUES_DISPLAY_SET,
} from './type';

import { getItems, updateItem, bulkAction } from 'lib/store';
import { accountingApi } from 'lib/api';

const STATUS_REVENUES_ITEM = {
	store: 'revenues',
	saving: REVENUES_ITEM_SAVING,
	saved: REVENUES_ITEM_SAVED,
	failed: REVENUES_ITEM_FAILED,
	order: 'name',
};
const STATUS_REVENUE = {
	store: 'revenues',
	loading: REVENUES_LOADING,
	loaded: REVENUES_LOADED,
	failed: REVENUES_FAILED,
	order: 'name',
};

export const setCreateItem = item => ({ type: REVENUES_ITEM_ADDED, item });
export const setUpdateItem = (id, item) => (dispatch, getState) =>
	updateItem(accountingApi.revenues.update, id, item, STATUS_REVENUES_ITEM, dispatch, getState().revenues);
export const setGetItems = args => (dispatch, getState) =>
	getItems(accountingApi.revenues.list, dispatch, STATUS_REVENUE, args, getState().revenues);
export const setOrderBy = (orderby, order) => setGetItems({ orderby, order });
export const setPage = page => setGetItems({ page });
export const setFilter = filterBy => setGetItems({ filterBy, orderby: '', page: 1 });
export const setSearch = search => setGetItems({ search, orderby: '', page: 1 });
export const setSelected = items => ({ type: REVENUES_SET_SELECTED, items: items.map(parseInt) });
export const setAllSelected = onoff => ({ type: REVENUES_SET_ALL_SELECTED, onoff });
export const setBulkAction = (action, ids) => (dispatch, getState) =>
	bulkAction(accountingApi.revenues.bulk, action, ids, STATUS_REVENUE, dispatch, getState().revenues);
