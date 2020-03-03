import {apiRequest, accountingApi} from "lib/api";
import {mergeWithTable, removeDefaults} from "../util";

export const loadRevenues = (params = {}, reduxer = s => s) => (dispatch, getState) => {
	const state = getState().revenues;
	const {table = {}, rows} = state;
	const tableData = reduxer(mergeWithTable(table, params));
	const data = removeDefaults({...table, ...params});

	return dispatch({
		type: "REVENUES",
		payload: apiRequest(accountingApi.revenues.list(data)),
		meta: {table: tableData, ...data, saving: []},
	});
};
