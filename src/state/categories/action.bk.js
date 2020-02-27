/**
 * Internal dependencies
 */
import {
	CATEGORIES_LOADING,
	CATEGORIES_LOADED,
	CATEGORIES_FAILED,
	CATEGORIES_ITEM_SAVING,
	CATEGORIES_ITEM_FAILED,
	CATEGORIES_ITEM_SAVED,
	CATEGORIES_SET_SELECTED,
	CATEGORIES_SET_ALL_SELECTED,
	CATEGORIES_DISPLAY_SET,
} from './type';

import { tableAction, createAction, updateAction, processRequest } from 'lib/store';
import { eAccountingApi } from 'lib/api';

const STATUS_CATEGORIES_ITEM = {
	store: 'categories',
	saving: CATEGORIES_ITEM_SAVING,
	saved: CATEGORIES_ITEM_SAVED,
	failed: CATEGORIES_ITEM_FAILED,
	order: 'name',
};
const STATUS_CATEGORY = {
	store: 'categories',
	saving: CATEGORIES_LOADING,
	saved: CATEGORIES_LOADED,
	failed: CATEGORIES_FAILED,
	order: 'name',
};

export const createCategory = item => createAction( eAccountingApi.categories.create, item, STATUS_CATEGORIES_ITEM );
export const updateCategory = ( id, item ) => updateAction( eAccountingApi.categories.update, id, item, STATUS_CATEGORIES_ITEM );
export const performTableAction = ( action, ids ) => tableAction( eAccountingApi.categories.bulk, action, ids, STATUS_CATEGORIES_ITEM );
export const getCategories = args => ( dispatch, getState ) => processRequest( eAccountingApi.categories.list, dispatch, STATUS_CATEGORY, args, getState().categories );
export const setOrderBy = ( orderby, order ) => getCategories( { orderby, order } );
export const setPage = page => getCategories( { page } );
export const setFilter = ( filterBy ) => getCategories( { filterBy, orderby: '', page: 0 } );
export const setSearch = ( search ) => getCategories( { search, orderby: '', page: 0 } );
export const setSelected = items => ( { type: CATEGORIES_SET_SELECTED, items: items.map( parseInt ) } );
export const setAllSelected = onoff => ( { type: CATEGORIES_SET_ALL_SELECTED, onoff } );
export const setTable = table => getCategories( table );
export const setDisplay = ( displayType, displaySelected ) => ( { type: CATEGORIES_DISPLAY_SET, displayType, displaySelected } );
