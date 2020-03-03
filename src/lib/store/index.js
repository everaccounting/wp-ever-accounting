import {apiRequest} from "lib/api";
import {mergeWithTable, removeDefaults, isEqual} from 'lib/table'
import {translate as __} from 'lib/locale';

/**
 * Get items and set to state
 *
 * @param endpoint
 * @param dispatch
 * @param statuses
 * @param params
 * @param state
 * @param reduxer
 * @returns {boolean|*}
 */
export const getItems = (endpoint, dispatch, statuses, params = {}, state = {}, reduxer = s => s) => {
	const {table = {}, rows} = state;
	const tableData = reduxer(mergeWithTable(table, params));
	const data = removeDefaults({...table, ...params});

	// If it's the same as our current store then ignore
	// if (isEqual(data, table) && rows.length > 0 && isEqual(params, {})) {
	// 	return false;
	// }

	apiRequest(endpoint(data))
		.then(res => {
			dispatch({type: statuses.loaded, ...res});
		})
		.catch(error => {
			dispatch({type: statuses.failed, error});
		});
	return dispatch({table: tableData, type: statuses.loading, ...data, saving: []});
};

/**
 * Update items
 * @param endpoint
 * @param id
 * @param item
 * @param statuses
 * @param dispatch
 * @param state
 * @returns {*}
 */
export const updateItem = (endpoint, id, item, statuses, dispatch, state) => {
	const {table} = state;
	apiRequest(endpoint(id, item))
		.then(res => {
			dispatch({type: statuses.saved, item: res.data, total: res.total, saving: [item.id]});
		})
		.catch(error => {
			dispatch({type: statuses.failed, error, item, saving: [item.id]});
		});

	return dispatch({type: statuses.saving, table, item, saving: [item.id]});
};

/**
 * Handle bulk actions
 * @param endpoint
 * @param action
 * @param ids
 * @param statuses
 * @param dispatch
 * @param state
 * @param extra
 */
export const bulkAction = (endpoint, action, ids, statuses, dispatch, state, extra = {}) => {
	const {table, total} = state;
	const params = {
		items: ids ? [ids] : table.selected,
		action,
	};

	table.page = 1;

	if (action === 'delete' && !confirm(__('Are you sure you want to delete this item?', 'Are you sure you want to delete the selected items?', {count: params.items.length}))) {
		return;
	}

	const tableData = mergeWithTable(table, params);
	const data = {...params, ...extra};

	apiRequest(endpoint(action, data, removeDefaults(table, status.order)))
		.then(res => {
			dispatch({type: statuses.loaded, ...res, saving: params.items});
		})
		.catch(error => {
			dispatch({type: statuses.failed, error, saving: params.items});
		});
	return dispatch({type: statuses.loading, table: tableData, saving: params.items});
};
