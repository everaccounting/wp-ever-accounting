/**
 * Internal dependencies
 */
import {getItems, updateItem, bulkAction} from "lib/store";
import {accountingApi} from "lib/api";

import {
	RECONCILIATIONS_LOADING,
	RECONCILIATIONS_LOADED,
	RECONCILIATIONS_FAILED,
	RECONCILIATIONS_ITEM_SAVING,
	RECONCILIATIONS_ITEM_FAILED,
	RECONCILIATIONS_ITEM_SAVED,
	RECONCILIATIONS_SET_SELECTED,
	RECONCILIATIONS_SET_ALL_SELECTED,
	RECONCILIATIONS_ITEM_ADDED
} from './type';

const STATUS_RECONCILIATIONS_ITEM = {
	store: 'reconciliations',
	saving: RECONCILIATIONS_ITEM_SAVING,
	saved: RECONCILIATIONS_ITEM_SAVED,
	failed: RECONCILIATIONS_ITEM_FAILED,
	order: 'name',
};
const STATUS_RECONCILIATION = {
	store: 'reconciliations',
	loading: RECONCILIATIONS_LOADING,
	loaded: RECONCILIATIONS_LOADED,
	failed: RECONCILIATIONS_FAILED,
	order: 'name',
};

export const setCreateItem = item => ({type: RECONCILIATIONS_ITEM_ADDED, item});
export const setUpdateItem = (id, item) => (dispatch, getState) => updateItem(accountingApi.reconciliations.update, id, item, STATUS_RECONCILIATIONS_ITEM, dispatch, getState().reconciliations);
export const setGetItems = args => (dispatch, getState) => getItems(accountingApi.reconciliations.list, dispatch, STATUS_RECONCILIATION, args, getState().reconciliations);
export const setOrderBy = (orderby, order) => setGetItems({orderby, order});
export const setPage = page => setGetItems({page});
export const setFilter = (filterBy) => setGetItems({filterBy, orderby: '', page: 1});
export const setSearch = (search) => setGetItems({search, orderby: '', page: 1});
export const setSelected = items => ({type: RECONCILIATIONS_SET_SELECTED, items: items.map(parseInt)});
export const setAllSelected = onoff => ({type: RECONCILIATIONS_SET_ALL_SELECTED, onoff});
export const setBulkAction = (action, ids ) =>  (dispatch, getState) => bulkAction(accountingApi.reconciliations.bulk, action, ids, STATUS_RECONCILIATION, dispatch, getState().reconciliations);
