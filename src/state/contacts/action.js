/**
 * Internal dependencies
 */
import {
	CONTACTS_LOADING,
	CONTACTS_LOADED,
	CONTACTS_FAILED,
	CONTACTS_ITEM_SAVING,
	CONTACTS_ITEM_FAILED,
	CONTACTS_ITEM_SAVED,
	CONTACTS_SET_SELECTED,
	CONTACTS_SET_ALL_SELECTED,
	CONTACTS_DISPLAY_SET,
} from './type';

import { tableAction, createAction, updateAction, processRequest } from 'lib/store';
import { eAccountingApi } from 'lib/api';

const STATUS_CONTACTS_ITEM = {
	store: 'contacts',
	saving: CONTACTS_ITEM_SAVING,
	saved: CONTACTS_ITEM_SAVED,
	failed: CONTACTS_ITEM_FAILED,
	order: 'name',
};
const STATUS_CONTACT = {
	store: 'contacts',
	saving: CONTACTS_LOADING,
	saved: CONTACTS_LOADED,
	failed: CONTACTS_FAILED,
	order: 'name',
};

export const createContact = item => createAction( eAccountingApi.contacts.create, item, STATUS_CONTACTS_ITEM );
export const updateContact = ( id, item ) => updateAction( eAccountingApi.contacts.update, id, item, STATUS_CONTACTS_ITEM );
export const performTableAction = ( action, ids ) => tableAction( eAccountingApi.contacts.bulk, action, ids, STATUS_CONTACTS_ITEM );
export const getContacts = args => ( dispatch, getState ) => processRequest( eAccountingApi.contacts.list, dispatch, STATUS_CONTACT, args, getState().contacts );
export const setOrderBy = ( orderby, order ) => getContacts( { orderby, order } );
export const setPage = page => getContacts( { page } );
export const setFilter = ( filterBy ) => getContacts( { filterBy, orderby: '', page: 0 } );
export const setSearch = ( search ) => getContacts( { search, orderby: '', page: 0 } );
export const setSelected = items => ( { type: CONTACTS_SET_SELECTED, items: items.map( parseInt ) } );
export const setAllSelected = onoff => ( { type: CONTACTS_SET_ALL_SELECTED, onoff } );
export const setTable = table => getContacts( table );
export const setDisplay = ( displayType, displaySelected ) => ( { type: CONTACTS_DISPLAY_SET, displayType, displaySelected } );
