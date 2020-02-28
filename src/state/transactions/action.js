/**
 * Internal dependencies
 */
import {
	TRANSACTIONS_LOADING,
	TRANSACTIONS_LOADED,
	TRANSACTIONS_FAILED
} from './type';

import {getItems} from "lib/store";
import {eAccountingApi} from "lib/api";

const STATUS_TRANSACTION = {
	store: 'transactions',
	loading: TRANSACTIONS_LOADING,
	loaded: TRANSACTIONS_LOADED,
	failed: TRANSACTIONS_FAILED,
	order: 'paid_at',
};

export const setGetItems = args => (dispatch, getState) => getItems(eAccountingApi.transactions.list, dispatch, STATUS_TRANSACTION, args, getState().transactions);
export const setOrderBy = (orderby, order) => setGetItems({orderby, order});
export const setPage = page => setGetItems({page});
export const setFilter = (filterBy) => setGetItems({filterBy,orderby: '', page: 1});
export const setSearch = (search) => getItems({search, orderby: '', page: 1});
