import {initialRevenues} from "./index";
import {STATUS_IN_PROGRESS, STATUS_COMPLETE, STATUS_FAILED} from "status";
import {setTable, setSaving, setTableAllSelected, setTableSelected} from "../util";

const revenues = (state = initialRevenues, action) => {
	switch (action.type) {
		case "REVENUES_LOADING":
			return {...state, table: setTable(state, action), status: STATUS_IN_PROGRESS, saving: setSaving(state, action)};

		case "REVENUES_SUCCESS":
			return {...state, status:STATUS_COMPLETE, rows: action.payload.data, total: action.payload.total || state.total, table: {...state.table, selected:[]}};

		case "REVENUES_FAILED":
			return {...state, status: STATUS_FAILED};

		case "REVENUES_ALL_SELECTED":
			return {...state, table: setTableAllSelected(state.table, state.rows, action.payload)};

		case "REVENUES_SELECTED":
			return {...state, table: setTableSelected(state.table, action.ids)};

		default:
			return state;
	}
};

export default revenues;