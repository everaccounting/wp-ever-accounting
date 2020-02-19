/**
 * Internal dependencies
 */
import {
	ACCOUNTS_LOADING,
	ACCOUNTS_LOADED,
	ACCOUNTS_FAILED,
	ACCOUNTS_ITEM_SAVING,
	ACCOUNTS_ITEM_FAILED,
	ACCOUNTS_ITEM_SAVED,
	ACCOUNTS_SET_SELECTED,
	ACCOUNTS_SET_ALL_SELECTED,
	ACCOUNTS_DISPLAY_SET,
} from './type';

import {tableAction, createAction, updateAction, processRequest} from 'lib/store';
import {eAccountingApi} from 'lib/api';

const STATUS_ACCOUNTS_ITEM = {
	store: 'accounts',
	saving: ACCOUNTS_ITEM_SAVING,
	saved: ACCOUNTS_ITEM_SAVED,
	failed: ACCOUNTS_ITEM_FAILED,
	order: 'name',
};
const STATUS_ACCOUNT = {
	store: 'accounts',
	saving: ACCOUNTS_LOADING,
	saved: ACCOUNTS_LOADED,
	failed: ACCOUNTS_FAILED,
	order: 'name',
};

export const createAccount = item => createAction(eAccountingApi.accounts.create, item, STATUS_ACCOUNTS_ITEM);
export const updateAccount = (id, item) => updateAction(eAccountingApi.accounts.update, id, item, STATUS_ACCOUNTS_ITEM);
export const performTableAction = (action, ids) => tableAction(eAccountingApi.accounts.bulk, action, ids, STATUS_ACCOUNTS_ITEM);
export const getAccounts = args => (dispatch, getState) => processRequest(eAccountingApi.accounts.list, dispatch, STATUS_ACCOUNT, args, getState().accounts);
export const setOrderBy = (orderby, order) => getAccounts({orderby, order});
export const setPage = page => getAccounts({page});
export const setFilter = (filterBy) => getAccounts({filterBy, orderby: '', page: 0});
export const setSearch = (search) => getAccounts({search, orderby: '', page: 0});
export const setSelected = items => ({type: ACCOUNTS_SET_SELECTED, items: items.map(parseInt)});
export const setAllSelected = onoff => ({type: ACCOUNTS_SET_ALL_SELECTED, onoff});
export const setTable = table => getAccounts(table);
export const setDisplay = (displayType, displaySelected) => ({
	type: ACCOUNTS_DISPLAY_SET,
	displayType,
	displaySelected
});
