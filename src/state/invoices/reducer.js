/**
 * Internal dependencies
 */

import {
	INVOICES_LOADED,
	INVOICES_LOADING,
	INVOICES_FAILED,
	INVOICES_SET_SELECTED,
	INVOICES_SET_ALL_SELECTED,
	INVOICES_ITEM_SAVING,
	INVOICES_ITEM_SAVED,
	INVOICES_ITEM_FAILED,
	INVOICES_DISPLAY_SET,
} from './type';
import { STATUS_IN_PROGRESS, STATUS_FAILED, STATUS_COMPLETE } from 'lib/status';
import { setTableSelected, setTableAllSelected, clearSelected } from 'lib/table';
import { setTable, setRows, setTotal, setItem, setSaving, removeSaving, restoreToOriginal } from 'lib/store';
import notify from "../../lib/notify";
export default function accounts( state = {}, action ) {
	switch ( action.type ) {
		case INVOICES_LOADING:
			return { ... state, table: setTable( state, action ), status: STATUS_IN_PROGRESS, saving: [] };

		case INVOICES_LOADED:
			return { ... state, rows: setRows( state, action ), status: STATUS_COMPLETE, total: setTotal( state, action ), table: clearSelected( state.table ) };

		case INVOICES_ITEM_SAVING:
			return { ... state, table: clearSelected( setTable( state, action ) ), saving: setSaving( state, action ), rows: setItem( state, action ) };

		case INVOICES_ITEM_SAVED:
			return { ... state, rows: setRows( state, action ), total: setTotal( state, action ), saving: removeSaving( state, action ) };

		case INVOICES_SET_ALL_SELECTED:
			return { ... state, table: setTableAllSelected( state.table, state.rows, action.onoff ) };

		case INVOICES_SET_SELECTED:
			return { ... state, table: setTableSelected( state.table, action.items ) };

		case INVOICES_FAILED:
			return { ... state, status: STATUS_FAILED, saving: [] };

		case INVOICES_ITEM_FAILED:
			return { ... state, saving: removeSaving( state, action ), rows: restoreToOriginal( state, action ) };

		case INVOICES_DISPLAY_SET:
			return { ... state, table: { ... state.table, displayType: action.displayType, displaySelected: action.displaySelected } };
	}

	return state;
}
