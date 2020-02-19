/**
 * Internal dependencies
 */

import {
	ACCOUNTS_LOADED,
	ACCOUNTS_LOADING,
	ACCOUNTS_FAILED,
	ACCOUNTS_SET_SELECTED,
	ACCOUNTS_SET_ALL_SELECTED,
	ACCOUNTS_ITEM_SAVING,
	ACCOUNTS_ITEM_SAVED,
	ACCOUNTS_ITEM_FAILED,
	ACCOUNTS_DISPLAY_SET,
} from './type';
import { STATUS_IN_PROGRESS, STATUS_FAILED, STATUS_COMPLETE } from 'lib/status';
import { setTableSelected, setTableAllSelected, clearSelected } from 'lib/table';
import { setTable, setRows, setTotal, setItem, setSaving, removeSaving, restoreToOriginal } from 'lib/store';
import notify from "../../lib/notify";
export default function accounts( state = {}, action ) {
	switch ( action.type ) {
		case ACCOUNTS_LOADING:
			return { ... state, table: setTable( state, action ), status: STATUS_IN_PROGRESS, saving: [] };

		case ACCOUNTS_LOADED:
			return { ... state, rows: setRows( state, action ), status: STATUS_COMPLETE, total: setTotal( state, action ), table: clearSelected( state.table ) };

		case ACCOUNTS_ITEM_SAVING:
			return { ... state, table: clearSelected( setTable( state, action ) ), saving: setSaving( state, action ), rows: setItem( state, action ) };

		case ACCOUNTS_ITEM_SAVED:
			return { ... state, rows: setRows( state, action ), total: setTotal( state, action ), saving: removeSaving( state, action ) };

		case ACCOUNTS_SET_ALL_SELECTED:
			return { ... state, table: setTableAllSelected( state.table, state.rows, action.onoff ) };

		case ACCOUNTS_SET_SELECTED:
			return { ... state, table: setTableSelected( state.table, action.items ) };

		case ACCOUNTS_FAILED:
			return { ... state, status: STATUS_FAILED, saving: [] };

		case ACCOUNTS_ITEM_FAILED:
			return { ... state, saving: removeSaving( state, action ), rows: restoreToOriginal( state, action ) };

		case ACCOUNTS_DISPLAY_SET:
			return { ... state, table: { ... state.table, displayType: action.displayType, displaySelected: action.displaySelected } };
	}

	return state;
}
