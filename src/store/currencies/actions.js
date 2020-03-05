import { apiRequest, accountingApi } from 'lib/api';
import { mergeWithTable, removeDefaults } from '../util';
import { __ } from '@wordpress/i18n';

export const fetchCurrencies = (params = {}, reduxer = s => s) => (dispatch, getState) => {
	const state = getState().currencies;
	const { table = {}, rows } = state;
	const tableData = reduxer(mergeWithTable(table, params));
	const data = removeDefaults({ ...table, ...params });

	return dispatch({
		type: 'CURRENCIES',
		payload: apiRequest(accountingApi.currencies.list(data)),
		meta: { table: tableData, ...data, saving: [] },
	});
};

export const BulkAction = (action, ids) => (dispatch, getState) => {
	const state = getState().currencies;
	const { table } = state;
	const params = {
		items: ids ? [ids] : table.selected,
		action,
	};
	table.page = 1;

	if (
		action === 'delete' &&
		!confirm(
			__('Are you sure you want to delete this item?', 'Are you sure you want to delete the selected items?', {
				count: params.items.length,
			})
		)
	) {
		return false;
	}

	const tableData = mergeWithTable(table, params);

	return dispatch({
		type: 'CURRENCIES',
		payload: apiRequest(accountingApi.currencies.bulk(action, params, removeDefaults(table, 'paid_at'))),
		meta: { table: tableData, saving: params.items },
	});
};
