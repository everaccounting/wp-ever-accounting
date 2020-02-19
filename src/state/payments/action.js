/**
 * Internal dependencies
 */
import {
	PAYMENTS_LOADING,
	PAYMENTS_LOADED,
	PAYMENTS_FAILED,
	PAYMENTS_ITEM_SAVING,
	PAYMENTS_ITEM_FAILED,
	PAYMENTS_ITEM_SAVED,
	PAYMENTS_SET_SELECTED,
	PAYMENTS_SET_ALL_SELECTED,
	PAYMENTS_DISPLAY_SET,
} from './type';

import { tableAction, createAction, updateAction, processRequest } from 'lib/store';
import { eAccountingApi } from 'lib/api';

const STATUS_PAYMENTS_ITEM = {
	store: 'payments',
	saving: PAYMENTS_ITEM_SAVING,
	saved: PAYMENTS_ITEM_SAVED,
	failed: PAYMENTS_ITEM_FAILED,
	order: 'name',
};
const STATUS_PAYMENT = {
	store: 'payments',
	saving: PAYMENTS_LOADING,
	saved: PAYMENTS_LOADED,
	failed: PAYMENTS_FAILED,
	order: 'name',
};

export const createPayment = item => createAction( eAccountingApi.payments.create, item, STATUS_PAYMENTS_ITEM );
export const updatePayment = ( id, item ) => updateAction( eAccountingApi.payments.update, id, item, STATUS_PAYMENTS_ITEM );
export const performTableAction = ( action, ids ) => tableAction( eAccountingApi.payments.bulk, action, ids, STATUS_PAYMENTS_ITEM );
export const getPayments = args => ( dispatch, getState ) => processRequest( eAccountingApi.payments.list, dispatch, STATUS_PAYMENT, args, getState().payments );
export const setOrderBy = ( orderby, order ) => getPayments( { orderby, order } );
export const setPage = page => getPayments( { page } );
export const setFilter = ( filterBy ) => getPayments( { filterBy, orderby: '', page: 0 } );
export const setSearch = ( search ) => getPayments( { search, orderby: '', page: 0 } );
export const setSelected = items => ( { type: PAYMENTS_SET_SELECTED, items: items.map( parseInt ) } );
export const setAllSelected = onoff => ( { type: PAYMENTS_SET_ALL_SELECTED, onoff } );
export const setTable = table => getPayments( table );
export const setDisplay = ( displayType, displaySelected ) => ( { type: PAYMENTS_DISPLAY_SET, displayType, displaySelected } );
