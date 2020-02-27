/**
 * External dependencies
 */


/**
 * Internal dependencies
 */
import {getItems as loadItems} from "lib/store";
import {eAccountingApi} from "lib/api";

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
	added: CATEGORIES_ITEM_ADDED,
	order: 'name',
};
const STATUS_CATEGORY = {
	store: 'categories',
	loading: CATEGORIES_LOADING,
	loaded: CATEGORIES_LOADED,
	failed: CATEGORIES_FAILED,
	order: 'name',
};

export const getItems = args => ( dispatch, getState ) => loadItems( eAccountingApi.categories.list, dispatch, STATUS_CATEGORY, args, getState().categories );
export const setOrderBy = ( orderby, order ) => getItems( { orderby, order } );
export const setPage = page => getItems( { page } );
export const setFilter = ( filterBy ) => getItems( { filterBy, orderby: '', page: 0 } );
export const setSearch = ( search ) => getItems( { search, orderby: '', page: 0 } );
export const setSelected = items => ( { type: CATEGORIES_SET_SELECTED, items: items.map( parseInt ) } );
export const setAllSelected = onoff => ( { type: CATEGORIES_SET_ALL_SELECTED, onoff } );
export const setTable = table => getItems( table );
