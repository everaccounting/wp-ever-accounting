/**
 * Internal dependencies
 */

import {
	REVENUES_LOADED,
	REVENUES_LOADING,
	REVENUES_FAILED,
	REVENUES_SET_SELECTED,
	REVENUES_SET_ALL_SELECTED,
	REVENUES_ITEM_SAVING,
	REVENUES_ITEM_SAVED,
	REVENUES_ITEM_FAILED,
	REVENUES_DISPLAY_SET,
} from './type';
import { STATUS_IN_PROGRESS, STATUS_FAILED, STATUS_COMPLETE } from 'lib/status';
import { setTableSelected, setTableAllSelected, clearSelected } from 'lib/table';
import { setTable, setRows, setTotal, setItem, setSaving, removeSaving, restoreToOriginal } from 'lib/store';
import notify from "../../lib/notify";
export default function accounts( state = {}, action ) {
	switch ( action.type ) {
		case REVENUES_LOADING:
			return { ... state, table: setTable( state, action ), status: STATUS_IN_PROGRESS, saving: [] };

		case REVENUES_LOADED:
			return { ... state, rows: setRows( state, action ), status: STATUS_COMPLETE, total: setTotal( state, action ), table: clearSelected( state.table ) };

		case REVENUES_ITEM_SAVING:
			return { ... state, table: clearSelected( setTable( state, action ) ), saving: setSaving( state, action ), rows: setItem( state, action ) };

		case REVENUES_ITEM_SAVED:
			return { ... state, rows: setRows( state, action ), total: setTotal( state, action ), saving: removeSaving( state, action ) };

		case REVENUES_SET_ALL_SELECTED:
			return { ... state, table: setTableAllSelected( state.table, state.rows, action.onoff ) };

		case REVENUES_SET_SELECTED:
			return { ... state, table: setTableSelected( state.table, action.items ) };

		case REVENUES_FAILED:
			return { ... state, status: STATUS_FAILED, saving: [] };

		case REVENUES_ITEM_FAILED:
			return { ... state, saving: removeSaving( state, action ), rows: restoreToOriginal( state, action ) };

		case REVENUES_DISPLAY_SET:
			return { ... state, table: { ... state.table, displayType: action.displayType, displaySelected: action.displaySelected } };
	}

	return state;
}
