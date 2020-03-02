/**
 * Internal dependencies
 */
import {getItems, updateItem, bulkAction} from "lib/store";
import {accountingApi} from "lib/api";

import {
	CONTACTS_LOADING,
	CONTACTS_LOADED,
	CONTACTS_FAILED,
	CONTACTS_ITEM_SAVING,
	CONTACTS_ITEM_FAILED,
	CONTACTS_ITEM_SAVED,
	CONTACTS_SET_SELECTED,
	CONTACTS_SET_ALL_SELECTED,
	CONTACTS_ITEM_ADDED
} from './type';

const STATUS_CONTACTS_ITEM = {
	store: 'contacts',
	saving: CONTACTS_ITEM_SAVING,
	saved: CONTACTS_ITEM_SAVED,
	failed: CONTACTS_ITEM_FAILED,
	order: 'name',
};
const STATUS_CONTACT = {
	store: 'contacts',
	loading: CONTACTS_LOADING,
	loaded: CONTACTS_LOADED,
	failed: CONTACTS_FAILED,
	order: 'name',
};

export const setCreateItem = item => ({type: CONTACTS_ITEM_ADDED, item});
export const setUpdateItem = (id, item) => (dispatch, getState) => updateItem(accountingApi.contacts.update, id, item, STATUS_CONTACTS_ITEM, dispatch, getState().contacts);
export const setGetItems = args => (dispatch, getState) => getItems(accountingApi.contacts.list, dispatch, STATUS_CONTACT, args, getState().contacts);
export const setOrderBy = (orderby, order) => setGetItems({orderby, order});
export const setPage = page => setGetItems({page});
export const setFilter = (filterBy) => setGetItems({filterBy, orderby: '', page: 1});
export const setSearch = (search) => setGetItems({search, orderby: '', page: 1});
export const setSelected = items => ({type: CONTACTS_SET_SELECTED, items: items.map(parseInt)});
export const setAllSelected = onoff => ({type: CONTACTS_SET_ALL_SELECTED, onoff});
export const setBulkAction = (action, ids ) =>  (dispatch, getState) => bulkAction(accountingApi.contacts.bulk, action, ids, STATUS_CONTACT, dispatch, getState().contacts);
