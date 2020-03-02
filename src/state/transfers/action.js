/**
 * Internal dependencies
 */
import {getItems, updateItem, bulkAction} from "lib/store";
import {accountingApi} from "lib/api";

import {
	TRANSFERS_LOADING,
	TRANSFERS_LOADED,
	TRANSFERS_FAILED,
	TRANSFERS_ITEM_SAVING,
	TRANSFERS_ITEM_FAILED,
	TRANSFERS_ITEM_SAVED,
	TRANSFERS_SET_SELECTED,
	TRANSFERS_SET_ALL_SELECTED,
	TRANSFERS_ITEM_ADDED
} from './type';

const STATUS_TRANSFERS_ITEM = {
	store: 'transfers',
	saving: TRANSFERS_ITEM_SAVING,
	saved: TRANSFERS_ITEM_SAVED,
	failed: TRANSFERS_ITEM_FAILED,
	order: 'name',
};
const STATUS_TRANSFER = {
	store: 'transfers',
	loading: TRANSFERS_LOADING,
	loaded: TRANSFERS_LOADED,
	failed: TRANSFERS_FAILED,
	order: 'name',
};

export const setCreateItem = item => ({type: TRANSFERS_ITEM_ADDED, item});
export const setUpdateItem = (id, item) => (dispatch, getState) => updateItem(accountingApi.transfers.update, id, item, STATUS_TRANSFERS_ITEM, dispatch, getState().transfers);
export const setGetItems = args => (dispatch, getState) => getItems(accountingApi.transfers.list, dispatch, STATUS_TRANSFER, args, getState().transfers);
export const setOrderBy = (orderby, order) => setGetItems({orderby, order});
export const setPage = page => setGetItems({page});
export const setFilter = (filterBy) => setGetItems({filterBy, orderby: '', page: 1});
export const setSearch = (search) => setGetItems({search, orderby: '', page: 1});
export const setSelected = items => ({type: TRANSFERS_SET_SELECTED, items: items.map(parseInt)});
export const setAllSelected = onoff => ({type: TRANSFERS_SET_ALL_SELECTED, onoff});
export const setBulkAction = (action, ids ) =>  (dispatch, getState) => bulkAction(accountingApi.transfers.bulk, action, ids, STATUS_TRANSFER, dispatch, getState().transfers);
