/**
 * Internal dependencies
 */
import {
	BILLS_LOADING,
	BILLS_LOADED,
	BILLS_FAILED,
	BILLS_ITEM_SAVING,
	BILLS_ITEM_FAILED,
	BILLS_ITEM_SAVED,
	BILLS_SET_SELECTED,
	BILLS_SET_ALL_SELECTED,
	BILLS_DISPLAY_SET,
} from './type';

import { tableAction, createAction, updateAction, processRequest } from 'lib/store';
import { accountingApi } from 'lib/api';

const STATUS_BILLS_ITEM = {
	store: 'bills',
	saving: BILLS_ITEM_SAVING,
	saved: BILLS_ITEM_SAVED,
	failed: BILLS_ITEM_FAILED,
	order: 'name',
};
const STATUS_BILL = {
	store: 'bills',
	saving: BILLS_LOADING,
	saved: BILLS_LOADED,
	failed: BILLS_FAILED,
	order: 'name',
};

export const createBill = item => createAction( accountingApi.bills.create, item, STATUS_BILLS_ITEM );
export const updateBill = ( id, item ) => updateAction( accountingApi.bills.update, id, item, STATUS_BILLS_ITEM );
export const performTableAction = ( action, ids ) => tableAction( accountingApi.bills.bulk, action, ids, STATUS_BILLS_ITEM );
export const getBills = args => ( dispatch, getState ) => processRequest( accountingApi.bills.list, dispatch, STATUS_BILL, args, getState().bills );
export const setOrderBy = ( orderby, order ) => getBills( { orderby, order } );
export const setPage = page => getBills( { page } );
export const setFilter = ( filterBy ) => getBills( { filterBy, orderby: '', page: 0 } );
export const setSearch = ( search ) => getBills( { search, orderby: '', page: 0 } );
export const setSelected = items => ( { type: BILLS_SET_SELECTED, items: items.map( parseInt ) } );
export const setAllSelected = onoff => ( { type: BILLS_SET_ALL_SELECTED, onoff } );
export const setTable = table => getBills( table );
export const setDisplay = ( displayType, displaySelected ) => ( { type: BILLS_DISPLAY_SET, displayType, displaySelected } );
