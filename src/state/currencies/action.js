/**
 * Internal dependencies
 */
import {
	CURRENCIES_LOADING,
	CURRENCIES_LOADED,
	CURRENCIES_FAILED,
	CURRENCIES_ITEM_SAVING,
	CURRENCIES_ITEM_FAILED,
	CURRENCIES_ITEM_SAVED,
	CURRENCIES_SET_SELECTED,
	CURRENCIES_SET_ALL_SELECTED,
	CURRENCIES_DISPLAY_SET,
} from './type';

import { tableAction, createAction, updateAction, processRequest } from 'lib/store';
import { eAccountingApi } from 'lib/api';

const STATUS_CURRENCIES_ITEM = {
	store: 'currencies',
	saving: CURRENCIES_ITEM_SAVING,
	saved: CURRENCIES_ITEM_SAVED,
	failed: CURRENCIES_ITEM_FAILED,
	order: 'name',
};
const STATUS_CURRENCY = {
	store: 'currencies',
	saving: CURRENCIES_LOADING,
	saved: CURRENCIES_LOADED,
	failed: CURRENCIES_FAILED,
	order: 'name',
};

export const createCurrency = item => createAction( eAccountingApi.currencies.create, item, STATUS_CURRENCIES_ITEM );
export const updateCurrency = ( id, item ) => updateAction( eAccountingApi.currencies.update, id, item, STATUS_CURRENCIES_ITEM );
export const performTableAction = ( action, ids ) => tableAction( eAccountingApi.currencies.bulk, action, ids, STATUS_CURRENCIES_ITEM );
export const getCurrencies = args => ( dispatch, getState ) => processRequest( eAccountingApi.currencies.list, dispatch, STATUS_CURRENCY, args, getState().currencies );
export const setOrderBy = ( orderby, order ) => getCurrencies( { orderby, order } );
export const setPage = page => getCurrencies( { page } );
export const setFilter = ( filterBy ) => getCurrencies( { filterBy, orderby: '', page: 0 } );
export const setSearch = ( search ) => getCurrencies( { search, orderby: '', page: 0 } );
export const setSelected = items => ( { type: CURRENCIES_SET_SELECTED, items: items.map( parseInt ) } );
export const setAllSelected = onoff => ( { type: CURRENCIES_SET_ALL_SELECTED, onoff } );
export const setTable = table => getCurrencies( table );
export const setDisplay = ( displayType, displaySelected ) => ( { type: CURRENCIES_DISPLAY_SET, displayType, displaySelected } );
