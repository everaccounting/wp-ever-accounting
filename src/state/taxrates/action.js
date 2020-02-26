/**
 * Internal dependencies
 */
import {
	TAXRATES_LOADING,
	TAXRATES_LOADED,
	TAXRATES_FAILED,
	TAXRATES_ITEM_SAVING,
	TAXRATES_ITEM_FAILED,
	TAXRATES_ITEM_SAVED,
	TAXRATES_SET_SELECTED,
	TAXRATES_SET_ALL_SELECTED,
	TAXRATES_DISPLAY_SET,
} from './type';

import { tableAction, createAction, updateAction, processRequest } from 'lib/store';
import { eAccountingApi } from 'lib/api';

const STATUS_TAXRATES_ITEM = {
	store: 'taxrates',
	saving: TAXRATES_ITEM_SAVING,
	saved: TAXRATES_ITEM_SAVED,
	failed: TAXRATES_ITEM_FAILED,
	order: 'name',
};
const STATUS_TAXRATE = {
	store: 'taxrates',
	saving: TAXRATES_LOADING,
	saved: TAXRATES_LOADED,
	failed: TAXRATES_FAILED,
	order: 'name',
};

export const createItem = item => createAction( eAccountingApi.taxrates.create, item, STATUS_TAXRATES_ITEM );
export const updateItem = ( id, item ) => updateAction( eAccountingApi.taxrates.update, id, item, STATUS_TAXRATES_ITEM );
export const performTableAction = ( action, ids ) => tableAction( eAccountingApi.taxrates.bulk, action, ids, STATUS_TAXRATES_ITEM );
export const getItems = args => ( dispatch, getState ) => processRequest( eAccountingApi.taxrates.list, dispatch, STATUS_TAXRATE, args, getState().taxrates );
export const setOrderBy = ( orderby, order ) => getItems( { orderby, order } );
export const setPage = page => getItems( { page } );
export const setFilter = ( filterBy ) => getItems( { filterBy, orderby: '', page: 0 } );
export const setSearch = ( search ) => getItems( { search, orderby: '', page: 0 } );
export const setSelected = items => ( { type: TAXRATES_SET_SELECTED, items: items.map( parseInt ) } );
export const setAllSelected = onoff => ( { type: TAXRATES_SET_ALL_SELECTED, onoff } );
export const setTable = table => getItems( table );
export const setDisplay = ( displayType, displaySelected ) => ( { type: TAXRATES_DISPLAY_SET, displayType, displaySelected } );
