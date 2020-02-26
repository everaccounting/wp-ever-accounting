/**
 * Internal dependencies
 */

import {
	TAXRATES_LOADED,
	TAXRATES_LOADING,
	TAXRATES_FAILED,
	TAXRATES_SET_SELECTED,
	TAXRATES_SET_ALL_SELECTED,
	TAXRATES_ITEM_SAVING,
	TAXRATES_ITEM_SAVED,
	TAXRATES_ITEM_FAILED,
	TAXRATES_DISPLAY_SET,
} from './type';
import { STATUS_IN_PROGRESS, STATUS_FAILED, STATUS_COMPLETE } from 'lib/status';
import { setTableSelected, setTableAllSelected, clearSelected } from 'lib/table';
import { setTable, setRows, setTotal, setItem, setSaving, removeSaving, restoreToOriginal } from 'lib/store';
export default function accounts( state = {}, action ) {
	switch ( action.type ) {
		case TAXRATES_LOADING:
			return { ... state, table: setTable( state, action ), status: STATUS_IN_PROGRESS, saving: [] };

		case TAXRATES_LOADED:
			return { ... state, rows: setRows( state, action ), status: STATUS_COMPLETE, total: setTotal( state, action ), table: clearSelected( state.table ) };

		case TAXRATES_ITEM_SAVING:
			return { ... state, table: clearSelected( setTable( state, action ) ), saving: setSaving( state, action ), rows: setItem( state, action ) };

		case TAXRATES_ITEM_SAVED:
			return { ... state, rows: setRows( state, action ), total: setTotal( state, action ), saving: removeSaving( state, action ) };

		case TAXRATES_SET_ALL_SELECTED:
			return { ... state, table: setTableAllSelected( state.table, state.rows, action.onoff ) };

		case TAXRATES_SET_SELECTED:
			return { ... state, table: setTableSelected( state.table, action.items ) };

		case TAXRATES_FAILED:
			return { ... state, status: STATUS_FAILED, saving: [] };

		case TAXRATES_ITEM_FAILED:
			return { ... state, saving: removeSaving( state, action ), rows: restoreToOriginal( state, action ) };

		case TAXRATES_DISPLAY_SET:
			return { ... state, table: { ... state.table, displayType: action.displayType, displaySelected: action.displaySelected } };
	}

	return state;
}
