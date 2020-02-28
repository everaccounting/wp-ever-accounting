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
	TAXRATES_ITEM_ADDED,
	TAXRATES_ITEM_FAILED,
} from './type';
import {STATUS_IN_PROGRESS, STATUS_FAILED, STATUS_COMPLETE} from 'lib/status';
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

export default function categories(state = {}, action) {
	switch (action.type) {
		case TAXRATES_LOADING:
			return {...state, table: setTable(state, action), status: STATUS_IN_PROGRESS, saving: setSaving(state, action)};

		case TAXRATES_LOADED:
			return {
				...state,
				rows: action.data,
				status: STATUS_COMPLETE,
				total: setTotal(state, action),
				table: clearSelected(state.table)
			};

		case TAXRATES_ITEM_SAVING:
			return {
				...state,
				table: clearSelected(setTable(state, action)),
				saving: setSaving(state, action),
				rows: setUpdatedItem(state.rows, action)
			};

		case TAXRATES_ITEM_SAVED:
			return {...state, saving: removeSaving(state, action)};

		case TAXRATES_ITEM_ADDED:
			return {...state, rows: [action.item, ...state.rows], total: state.total + 1};

		case TAXRATES_SET_ALL_SELECTED:
			return {...state, table: setTableAllSelected(state.table, state.rows, action.onoff)};

		case TAXRATES_SET_SELECTED:
			return {...state, table: setTableSelected(state.table, action.items)};

		case TAXRATES_FAILED:
			return {...state, status: STATUS_FAILED, saving: []};

		case TAXRATES_ITEM_FAILED:
			return {...state, saving: removeSaving(state, action), rows: restoreToOriginal(state, action)};
	}

	return state;
}
