/**
 * Internal dependencies
 */
import {
	TRANSACTIONS_LOADING,
	TRANSACTIONS_LOADED,
	TRANSACTIONS_FAILED
} from './type';

import {processRequest} from 'lib/store';
import {eAccountingApi} from 'lib/api';

const STATUS_TRANSACTION = {
	store: 'transactions',
	saving: TRANSACTIONS_LOADING,
	saved: TRANSACTIONS_LOADED,
	failed: TRANSACTIONS_FAILED,
	order: 'name',
};

export const getTransactions = args => (dispatch, getState) => processRequest(eAccountingApi.transactions.list, dispatch, STATUS_TRANSACTION, args, getState().transactions);
export const setOrderBy = (orderby, order) => getTransactions({orderby, order});
export const setPage = page => getTransactions({page});
export const setFilter = (filter) => getTransactions({...filter,orderby: '', page: 0});
export const setSearch = (search) => getTransactions({search, orderby: '', page: 0});
export const setTable = table => getTransactions(table);
