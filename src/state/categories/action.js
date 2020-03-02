/**
 * External dependencies
 */


/**
 * Internal dependencies
 */
import {getItems, updateItem, bulkAction} from "lib/store";
import {accountingApi} from "lib/api";

import {
	CATEGORIES_LOADING,
	CATEGORIES_LOADED,
	CATEGORIES_FAILED,
	CATEGORIES_ITEM_SAVING,
	CATEGORIES_ITEM_FAILED,
	CATEGORIES_ITEM_SAVED,
	CATEGORIES_SET_SELECTED,
	CATEGORIES_SET_ALL_SELECTED,
	CATEGORIES_ITEM_ADDED
} from './type';

const STATUS_CATEGORIES_ITEM = {
	store: 'categories',
	saving: CATEGORIES_ITEM_SAVING,
	saved: CATEGORIES_ITEM_SAVED,
	failed: CATEGORIES_ITEM_FAILED,
	order: 'name',
};
const STATUS_CATEGORY = {
	store: 'categories',
	loading: CATEGORIES_LOADING,
	loaded: CATEGORIES_LOADED,
	failed: CATEGORIES_FAILED,
	order: 'name',
};
export const setCreateItem = item => ({type: CATEGORIES_ITEM_ADDED, item});
export const setUpdateItem = (id, item) => (dispatch, getState) => updateItem(accountingApi.categories.update, id, item, STATUS_CATEGORIES_ITEM, dispatch, getState().categories);
export const setGetItems = args => (dispatch, getState) => getItems(accountingApi.categories.list, dispatch, STATUS_CATEGORY, args, getState().categories);
export const setOrderBy = (orderby, order) => setGetItems({orderby, order});
export const setPage = page => setGetItems({page});
export const setFilter = (filterBy) => setGetItems({filterBy, orderby: '', page: 1});
export const setSearch = (search) => setGetItems({search, orderby: '', page: 1});
export const setSelected = items => ({type: CATEGORIES_SET_SELECTED, items: items.map(parseInt)});
export const setAllSelected = onoff => ({type: CATEGORIES_SET_ALL_SELECTED, onoff});
export const setBulkAction = (action, ids ) =>  (dispatch, getState) => bulkAction(accountingApi.categories.bulk, action, ids, STATUS_CATEGORY, dispatch, getState().categories);

