/**
 * Internal dependencies
 */
import {getItems, updateItem, bulkAction} from "lib/store";
import {accountingApi} from "lib/api";

import {
	ACCOUNTS_LOADING,
	ACCOUNTS_LOADED,
	ACCOUNTS_FAILED,
	ACCOUNTS_ITEM_SAVING,
	ACCOUNTS_ITEM_FAILED,
	ACCOUNTS_ITEM_SAVED,
	ACCOUNTS_SET_SELECTED,
	ACCOUNTS_SET_ALL_SELECTED,
	ACCOUNTS_ITEM_ADDED
} from './type';

const STATUS_ACCOUNTS_ITEM = {
	store: 'accounts',
	saving: ACCOUNTS_ITEM_SAVING,
	saved: ACCOUNTS_ITEM_SAVED,
	failed: ACCOUNTS_ITEM_FAILED,
	order: 'name',
};
const STATUS_ACCOUNT = {
	store: 'accounts',
	loading: ACCOUNTS_LOADING,
	loaded: ACCOUNTS_LOADED,
	failed: ACCOUNTS_FAILED,
	order: 'name',
};

export const setCreateItem = item => ({type: ACCOUNTS_ITEM_ADDED, item});
export const setUpdateItem = (id, item) => (dispatch, getState) => updateItem(accountingApi.accounts.update, id, item, STATUS_ACCOUNTS_ITEM, dispatch, getState().accounts);
export const setGetItems = args => (dispatch, getState) => getItems(accountingApi.accounts.list, dispatch, STATUS_ACCOUNT, args, getState().accounts);
export const setOrderBy = (orderby, order) => setGetItems({orderby, order});
export const setPage = page => setGetItems({page});
export const setFilter = (filterBy) => setGetItems({filterBy, orderby: '', page: 1});
export const setSearch = (search) => setGetItems({search, orderby: '', page: 1});
export const setSelected = items => ({type: ACCOUNTS_SET_SELECTED, items: items.map(parseInt)});
export const setAllSelected = onoff => ({type: ACCOUNTS_SET_ALL_SELECTED, onoff});
export const setBulkAction = (action, ids ) =>  (dispatch, getState) => bulkAction(accountingApi.accounts.bulk, action, ids, STATUS_ACCOUNT, dispatch, getState().accounts);
