/**
 * Internal dependencies
 */
import {
	REVENUES_LOADING,
	REVENUES_LOADED,
	REVENUES_FAILED,
	REVENUES_ITEM_SAVING,
	REVENUES_ITEM_FAILED,
	REVENUES_ITEM_SAVED,
	REVENUES_SET_SELECTED,
	REVENUES_SET_ALL_SELECTED,
	REVENUES_DISPLAY_SET,
} from './type';

import { tableAction, createAction, updateAction, processRequest } from 'lib/store';
import { eRevenueingApi } from 'lib/api';

const STATUS_REVENUES_ITEM = {
	store: 'revenues',
	saving: REVENUES_ITEM_SAVING,
	saved: REVENUES_ITEM_SAVED,
	failed: REVENUES_ITEM_FAILED,
	order: 'name',
};
const STATUS_REVENUE = {
	store: 'revenues',
	saving: REVENUES_LOADING,
	saved: REVENUES_LOADED,
	failed: REVENUES_FAILED,
	order: 'name',
};

export const createRevenue = item => createAction( eRevenueingApi.revenues.create, item, STATUS_REVENUES_ITEM );
export const updateRevenue = ( id, item ) => updateAction( eRevenueingApi.revenues.update, id, item, STATUS_REVENUES_ITEM );
export const performTableAction = ( action, ids ) => tableAction( eRevenueingApi.revenues.bulk, action, ids, STATUS_REVENUES_ITEM );
export const getRevenues = args => ( dispatch, getState ) => processRequest( eRevenueingApi.revenues.list, dispatch, STATUS_REVENUE, args, getState().revenues );
export const setOrderBy = ( orderby, order ) => getRevenues( { orderby, order } );
export const setPage = page => getRevenues( { page } );
export const setFilter = ( filterBy ) => getRevenues( { filterBy, orderby: '', page: 0 } );
export const setSearch = ( search ) => getRevenues( { search, orderby: '', page: 0 } );
export const setSelected = items => ( { type: REVENUES_SET_SELECTED, items: items.map( parseInt ) } );
export const setAllSelected = onoff => ( { type: REVENUES_SET_ALL_SELECTED, onoff } );
export const setTable = table => getRevenues( table );
export const setDisplay = ( displayType, displaySelected ) => ( { type: REVENUES_DISPLAY_SET, displayType, displaySelected } );
