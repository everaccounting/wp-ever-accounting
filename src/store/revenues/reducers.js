import {initialRevenues} from "./index";
import {STATUS_IN_PROGRESS, STATUS_COMPLETE} from "status";
import {setTable, setSaving} from "../util";

const revenues = (state = initialRevenues, action) => {
	switch (action.type) {
		case "REVENUES_LOADING":
			return {...state, table: setTable(state, action), status: STATUS_IN_PROGRESS, saving: setSaving(state, action)};

		case "REVENUES_SUCCESS":
			return {...state, status:STATUS_COMPLETE, rows: action.payload.data, total: action.payload.total || state.total, table: {...state.table, selected:[]}};

		case "REVENUE_SUCCESS":
			console.log(action);
			return {...state, revenue: action.payload.data};
		default:
			return state;
	}
};

export default revenues;
