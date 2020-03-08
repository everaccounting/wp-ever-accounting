/**
 * Internal dependencies
 */
import {
	INVOICES_LOADING,
	INVOICES_LOADED,
	INVOICES_FAILED,
	INVOICES_ITEM_SAVING,
	INVOICES_ITEM_FAILED,
	INVOICES_ITEM_SAVED,
	INVOICES_SET_SELECTED,
	INVOICES_SET_ALL_SELECTED,
	INVOICES_DISPLAY_SET,
} from './type';

import { tableAction, createAction, updateAction, processRequest } from 'lib/store';
import { accountingApi } from 'lib/api';

const STATUS_INVOICES_ITEM = {
	store: 'invoices',
	saving: INVOICES_ITEM_SAVING,
	saved: INVOICES_ITEM_SAVED,
	failed: INVOICES_ITEM_FAILED,
	order: 'name',
};
const STATUS_INVOICE = {
	store: 'invoices',
	saving: INVOICES_LOADING,
	saved: INVOICES_LOADED,
	failed: INVOICES_FAILED,
	order: 'name',
};

export const createInvoice = item => createAction(accountingApi.invoices.create, item, STATUS_INVOICES_ITEM);
export const updateInvoice = (id, item) => updateAction(accountingApi.invoices.update, id, item, STATUS_INVOICES_ITEM);
export const performTableAction = (action, ids) =>
	tableAction(accountingApi.invoices.bulk, action, ids, STATUS_INVOICES_ITEM);
export const getInvoices = args => (dispatch, getState) =>
	processRequest(accountingApi.invoices.list, dispatch, STATUS_INVOICE, args, getState().invoices);
export const setOrderBy = (orderby, order) => getInvoices({ orderby, order });
export const setPage = page => getInvoices({ page });
export const setFilter = filterBy => getInvoices({ filterBy, orderby: '', page: 0 });
export const setSearch = search => getInvoices({ search, orderby: '', page: 0 });
export const setSelected = items => ({ type: INVOICES_SET_SELECTED, items: items.map(parseInt) });
export const setAllSelected = onoff => ({ type: INVOICES_SET_ALL_SELECTED, onoff });
export const setTable = table => getInvoices(table);
export const setDisplay = (displayType, displaySelected) => ({
	type: INVOICES_DISPLAY_SET,
	displayType,
	displaySelected,
});
