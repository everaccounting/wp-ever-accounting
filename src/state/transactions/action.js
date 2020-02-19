/**
 * Internal dependencies
 */
import {
	TRANSACTIONS_LOADING,
	TRANSACTIONS_LOADED,
	TRANSACTIONS_FAILED,
	TRANSACTIONS_ITEM_SAVING,
	TRANSACTIONS_ITEM_FAILED,
	TRANSACTIONS_ITEM_SAVED,
	TRANSACTIONS_SET_SELECTED,
	TRANSACTIONS_SET_ALL_SELECTED,
	TRANSACTIONS_DISPLAY_SET,
} from './type';

import { tableAction, createAction, updateAction, processRequest } from 'lib/store';
import { eAccountingApi } from 'lib/api';

const STATUS_TRANSACTIONS_ITEM = {
	store: 'transactions',
	saving: TRANSACTIONS_ITEM_SAVING,
	saved: TRANSACTIONS_ITEM_SAVED,
	failed: TRANSACTIONS_ITEM_FAILED,
	order: 'name',
};
const STATUS_TRANSACTION = {
	store: 'transactions',
	saving: TRANSACTIONS_LOADING,
	saved: TRANSACTIONS_LOADED,
	failed: TRANSACTIONS_FAILED,
	order: 'name',
};

export const performTableAction = ( action, ids ) => tableAction( eAccountingApi.transactions.bulk, action, ids, STATUS_TRANSACTIONS_ITEM );
export const getTransactions = args => ( dispatch, getState ) => processRequest( eAccountingApi.transactions.list, dispatch, STATUS_TRANSACTION, args, getState().transactions );
export const setOrderBy = ( orderby, order ) => getTransactions( { orderby, order } );
export const setPage = page => getTransactions( { page } );
export const setFilter = ( filterBy ) => getTransactions( { filterBy, orderby: '', page: 0 } );
export const setSearch = ( search ) => getTransactions( { search, orderby: '', page: 0 } );
export const setSelected = items => ( { type: TRANSACTIONS_SET_SELECTED, items: items.map( parseInt ) } );
export const setAllSelected = onoff => ( { type: TRANSACTIONS_SET_ALL_SELECTED, onoff } );
export const setTable = table => getTransactions( table );
export const setDisplay = ( displayType, displaySelected ) => ( { type: TRANSACTIONS_DISPLAY_SET, displayType, displaySelected } );
