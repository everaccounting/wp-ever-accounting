/**
 * Internal dependencies
 */

import {
	PAYMENTS_LOADED,
	PAYMENTS_LOADING,
	PAYMENTS_FAILED,
	PAYMENTS_SET_SELECTED,
	PAYMENTS_SET_ALL_SELECTED,
	PAYMENTS_ITEM_SAVING,
	PAYMENTS_ITEM_SAVED,
	PAYMENTS_ITEM_FAILED,
	PAYMENTS_DISPLAY_SET,
} from './type';
import { STATUS_IN_PROGRESS, STATUS_FAILED, STATUS_COMPLETE } from 'lib/status';
import { setTableSelected, setTableAllSelected, clearSelected } from 'lib/table';
import { setTable, setRows, setTotal, setItem, setSaving, removeSaving, restoreToOriginal } from 'lib/store';
import notify from "../../lib/notify";
export default function accounts( state = {}, action ) {
	switch ( action.type ) {
		case PAYMENTS_LOADING:
			return { ... state, table: setTable( state, action ), status: STATUS_IN_PROGRESS, saving: [] };

		case PAYMENTS_LOADED:
			return { ... state, rows: setRows( state, action ), status: STATUS_COMPLETE, total: setTotal( state, action ), table: clearSelected( state.table ) };

		case PAYMENTS_ITEM_SAVING:
			return { ... state, table: clearSelected( setTable( state, action ) ), saving: setSaving( state, action ), rows: setItem( state, action ) };

		case PAYMENTS_ITEM_SAVED:
			return { ... state, rows: setRows( state, action ), total: setTotal( state, action ), saving: removeSaving( state, action ) };

		case PAYMENTS_SET_ALL_SELECTED:
			return { ... state, table: setTableAllSelected( state.table, state.rows, action.onoff ) };

		case PAYMENTS_SET_SELECTED:
			return { ... state, table: setTableSelected( state.table, action.items ) };

		case PAYMENTS_FAILED:
			return { ... state, status: STATUS_FAILED, saving: [] };

		case PAYMENTS_ITEM_FAILED:
			return { ... state, saving: removeSaving( state, action ), rows: restoreToOriginal( state, action ) };

		case PAYMENTS_DISPLAY_SET:
			return { ... state, table: { ... state.table, displayType: action.displayType, displaySelected: action.displaySelected } };
	}

	return state;
}
