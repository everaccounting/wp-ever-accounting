/**
 * Internal dependencies
 */

import {
	TRANSACTIONS_LOADED,
	TRANSACTIONS_LOADING,
	TRANSACTIONS_FAILED,
	TRANSACTIONS_SET_SELECTED,
	TRANSACTIONS_SET_ALL_SELECTED,
	TRANSACTIONS_ITEM_SAVING,
	TRANSACTIONS_ITEM_SAVED,
	TRANSACTIONS_ITEM_FAILED,
	TRANSACTIONS_DISPLAY_SET,
} from './type';
import { STATUS_IN_PROGRESS, STATUS_FAILED, STATUS_COMPLETE } from 'lib/status';
import { setTableSelected, setTableAllSelected, clearSelected } from 'lib/table';
import { setTable, setRows, setTotal, setItem, setSaving, removeSaving, restoreToOriginal } from 'lib/store';
import notify from "../../lib/notify";
export default function accounts( state = {}, action ) {
	switch ( action.type ) {
		case TRANSACTIONS_LOADING:
			return { ... state, table: setTable( state, action ), status: STATUS_IN_PROGRESS, saving: [] };

		case TRANSACTIONS_LOADED:
			return { ... state, rows: setRows( state, action ), status: STATUS_COMPLETE, total: setTotal( state, action ), table: clearSelected( state.table ) };

		case TRANSACTIONS_ITEM_SAVING:
			return { ... state, table: clearSelected( setTable( state, action ) ), saving: setSaving( state, action ), rows: setItem( state, action ) };

		case TRANSACTIONS_ITEM_SAVED:
			return { ... state, rows: setRows( state, action ), total: setTotal( state, action ), saving: removeSaving( state, action ) };

		case TRANSACTIONS_SET_ALL_SELECTED:
			return { ... state, table: setTableAllSelected( state.table, state.rows, action.onoff ) };

		case TRANSACTIONS_SET_SELECTED:
			return { ... state, table: setTableSelected( state.table, action.items ) };

		case TRANSACTIONS_FAILED:
			return { ... state, status: STATUS_FAILED, saving: [] };

		case TRANSACTIONS_ITEM_FAILED:
			return { ... state, saving: removeSaving( state, action ), rows: restoreToOriginal( state, action ) };

		case TRANSACTIONS_DISPLAY_SET:
			return { ... state, table: { ... state.table, displayType: action.displayType, displaySelected: action.displaySelected } };
	}

	return state;
}
