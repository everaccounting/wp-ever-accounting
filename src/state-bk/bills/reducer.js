/**
 * Internal dependencies
 */

import {
	BILLS_LOADED,
	BILLS_LOADING,
	BILLS_FAILED,
	BILLS_SET_SELECTED,
	BILLS_SET_ALL_SELECTED,
	BILLS_ITEM_SAVING,
	BILLS_ITEM_SAVED,
	BILLS_ITEM_FAILED,
	BILLS_DISPLAY_SET,
} from './type';
import { STATUS_IN_PROGRESS, STATUS_FAILED, STATUS_COMPLETE } from 'status';
import { setTableSelected, setTableAllSelected, clearSelected } from 'lib/table';
import { setTable, setRows, setTotal, setItem, setSaving, removeSaving, restoreToOriginal } from 'lib/store';
import notify from '../../lib/notify';
export default function accounts(state = {}, action) {
	switch (action.type) {
		case BILLS_LOADING:
			return { ...state, table: setTable(state, action), status: STATUS_IN_PROGRESS, saving: [] };

		case BILLS_LOADED:
			return {
				...state,
				rows: setRows(state, action),
				status: STATUS_COMPLETE,
				total: setTotal(state, action),
				table: clearSelected(state.table),
			};

		case BILLS_ITEM_SAVING:
			return {
				...state,
				table: clearSelected(setTable(state, action)),
				saving: setSaving(state, action),
				rows: setItem(state, action),
			};

		case BILLS_ITEM_SAVED:
			return {
				...state,
				rows: setRows(state, action),
				total: setTotal(state, action),
				saving: removeSaving(state, action),
			};

		case BILLS_SET_ALL_SELECTED:
			return { ...state, table: setTableAllSelected(state.table, state.rows, action.onoff) };

		case BILLS_SET_SELECTED:
			return { ...state, table: setTableSelected(state.table, action.items) };

		case BILLS_FAILED:
			return { ...state, status: STATUS_FAILED, saving: [] };

		case BILLS_ITEM_FAILED:
			return { ...state, saving: removeSaving(state, action), rows: restoreToOriginal(state, action) };

		case BILLS_DISPLAY_SET:
			return {
				...state,
				table: { ...state.table, displayType: action.displayType, displaySelected: action.displaySelected },
			};
	}

	return state;
}
