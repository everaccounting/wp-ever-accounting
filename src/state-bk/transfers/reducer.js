/**
 * Internal dependencies
 */
/**
 * Internal dependencies
 */

import {
	TRANSFERS_LOADED,
	TRANSFERS_LOADING,
	TRANSFERS_FAILED,
	TRANSFERS_SET_SELECTED,
	TRANSFERS_SET_ALL_SELECTED,
	TRANSFERS_ITEM_SAVING,
	TRANSFERS_ITEM_SAVED,
	TRANSFERS_ITEM_ADDED,
	TRANSFERS_ITEM_FAILED,
} from './type';
import {STATUS_IN_PROGRESS, STATUS_FAILED, STATUS_COMPLETE} from 'status';
import {
	setTable,
	setTotal,
	setUpdatedItem,
	setSaving,
	removeSaving,
	restoreToOriginal,
	setTableSelected,
	setTableAllSelected,
	clearSelected
} from 'lib/table';

export default function accounts(state = {}, action) {
	switch (action.type) {
		case TRANSFERS_LOADING:
			return {...state, table: setTable(state, action), status: STATUS_IN_PROGRESS, saving: setSaving(state, action)};

		case TRANSFERS_LOADED:
			return {
				...state,
				rows: action.data,
				status: STATUS_COMPLETE,
				total: setTotal(state, action),
				table: clearSelected(state.table)
			};

		case TRANSFERS_ITEM_SAVING:
			return {
				...state,
				table: clearSelected(setTable(state, action)),
				saving: setSaving(state, action),
				rows: setUpdatedItem(state.rows, action)
			};

		case TRANSFERS_ITEM_SAVED:
			return {...state, saving: removeSaving(state, action)};

		case TRANSFERS_ITEM_ADDED:
			return {...state, rows: [action.item, ...state.rows], total: state.total + 1};

		case TRANSFERS_SET_ALL_SELECTED:
			return {...state, table: setTableAllSelected(state.table, state.rows, action.onoff)};

		case TRANSFERS_SET_SELECTED:
			return {...state, table: setTableSelected(state.table, action.items)};

		case TRANSFERS_FAILED:
			return {...state, status: STATUS_FAILED, saving: []};

		case TRANSFERS_ITEM_FAILED:
			return {...state, saving: removeSaving(state, action), rows: restoreToOriginal(state, action)};
	}

	return state;
}
