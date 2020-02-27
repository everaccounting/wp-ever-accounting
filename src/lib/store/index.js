import {apiRequest} from "lib/api";
import {mergeWithTable, removeDefaults, isEqual} from 'lib/table'

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
	const data = removeDefaults({...table, ...params}, statuses.order);

	// If it's the same as our current store then ignore
	// if (isEqual(data, table) && rows.length > 0 && isEqual(params, {})) {
	// 	return false;
	// }

	apiRequest(endpoint(data))
		.then(json => {
			console.log(json);
			dispatch({type: statuses.loaded, ...json});
		})
		.catch(error => {
			dispatch({type: statuses.failed, error});
		});
	return dispatch({table: tableData, type: statuses.loading, ...data});
};



export const createItem = (endpoint, dispatch, status, params = {}, state = {}, reduxer = s => s) => {

};

export const updateItem = (endpoint, dispatch, status, params = {}, state = {}, reduxer = s => s) => {

};


export const tableAction = (endpoint, dispatch, status, params = {}, state = {}, reduxer = s => s) => {

};
