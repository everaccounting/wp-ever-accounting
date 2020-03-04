import {apiRequest, accountingApi} from "lib/api";

export const loadRevenue = (id) => (dispatch) => {
	return dispatch({
		type: "REVENUE",
		payload: apiRequest(accountingApi.revenues.get(id)),
		// meta: {table: tableData, ...data, saving: []},
	});
};
