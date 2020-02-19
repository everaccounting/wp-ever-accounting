/**
 * Internal dependencies
 */

import {
	CONTACTS_LOADED,
	CONTACTS_LOADING,
	CONTACTS_FAILED,
	CONTACTS_SET_SELECTED,
	CONTACTS_SET_ALL_SELECTED,
	CONTACTS_ITEM_SAVING,
	CONTACTS_ITEM_SAVED,
	CONTACTS_ITEM_FAILED,
	CONTACTS_DISPLAY_SET,
} from './type';
import { STATUS_IN_PROGRESS, STATUS_FAILED, STATUS_COMPLETE } from 'lib/status';
import { setTableSelected, setTableAllSelected, clearSelected } from 'lib/table';
import { setTable, setRows, setTotal, setItem, setSaving, removeSaving, restoreToOriginal } from 'lib/store';
import notify from "../../lib/notify";
export default function contacts( state = {}, action ) {
	switch ( action.type ) {
		case CONTACTS_LOADING:
			return { ... state, table: setTable( state, action ), status: STATUS_IN_PROGRESS, saving: [] };

		case CONTACTS_LOADED:
			return { ... state, rows: setRows( state, action ), status: STATUS_COMPLETE, total: setTotal( state, action ), table: clearSelected( state.table ) };

		case CONTACTS_ITEM_SAVING:
			return { ... state, table: clearSelected( setTable( state, action ) ), saving: setSaving( state, action ), rows: setItem( state, action ) };

		case CONTACTS_ITEM_SAVED:
			return { ... state, rows: setRows( state, action ), total: setTotal( state, action ), saving: removeSaving( state, action ) };

		case CONTACTS_SET_ALL_SELECTED:
			return { ... state, table: setTableAllSelected( state.table, state.rows, action.onoff ) };

		case CONTACTS_SET_SELECTED:
			return { ... state, table: setTableSelected( state.table, action.items ) };

		case CONTACTS_FAILED:
			return { ... state, status: STATUS_FAILED, saving: [] };

		case CONTACTS_ITEM_FAILED:
			return { ... state, saving: removeSaving( state, action ), rows: restoreToOriginal( state, action ) };

		case CONTACTS_DISPLAY_SET:
			return { ... state, table: { ... state.table, displayType: action.displayType, displaySelected: action.displaySelected } };
	}

	return state;
}
