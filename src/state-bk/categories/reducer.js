/**
 * Internal dependencies
 */

import {
	CATEGORIES_LOADED,
	CATEGORIES_LOADING,
	CATEGORIES_FAILED,
	CATEGORIES_SET_SELECTED,
	CATEGORIES_SET_ALL_SELECTED,
	CATEGORIES_ITEM_SAVING,
	CATEGORIES_ITEM_SAVED,
	CATEGORIES_ITEM_ADDED,
	CATEGORIES_ITEM_FAILED,
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

export default function categories(state = {}, action) {
	switch (action.type) {
		case CATEGORIES_LOADING:
			return {...state, table: setTable(state, action), status: STATUS_IN_PROGRESS, saving: setSaving(state, action)};

		case CATEGORIES_LOADED:
			return {
				...state,
				rows: action.data,
				status: STATUS_COMPLETE,
				total: setTotal(state, action),
				table: clearSelected(state.table)
			};

		case CATEGORIES_ITEM_SAVING:
			return {
				...state,
				table: clearSelected(setTable(state, action)),
				saving: setSaving(state, action),
				rows: setUpdatedItem(state.rows, action)
			};

		case CATEGORIES_ITEM_SAVED:
			return {...state, saving: removeSaving(state, action)};

		case CATEGORIES_ITEM_ADDED:
			return {...state, rows: [action.item, ...state.rows], total: state.total + 1};

		case CATEGORIES_SET_ALL_SELECTED:
			return {...state, table: setTableAllSelected(state.table, state.rows, action.onoff)};

		case CATEGORIES_SET_SELECTED:
			return {...state, table: setTableSelected(state.table, action.items)};

		case CATEGORIES_FAILED:
			return {...state, status: STATUS_FAILED, saving: []};

		case CATEGORIES_ITEM_FAILED:
			return {...state, saving: removeSaving(state, action), rows: restoreToOriginal(state, action)};
	}

	return state;
}
