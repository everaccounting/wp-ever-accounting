import { apiRequest, accountingApi } from 'lib/api';
import { mergeWithTable, removeDefaults } from '../util';

export const fetchTransactions = (params = {}, reduxer = s => s) => (dispatch, getState) => {
	const state = getState().revenues;
	const { table = {}, rows } = state;
	const tableData = reduxer(mergeWithTable(table, params));
	const data = removeDefaults({ ...table, ...params }, 'paid_at');
	return dispatch({
		type: 'TRANSACTIONS',
		payload: apiRequest(accountingApi.transactions.list(data)),
		meta: { table: tableData, ...data, saving: [] },
	});
};

export const setFilter = filters => fetchTransactions({ filters, orderby: '', page: 1 });
