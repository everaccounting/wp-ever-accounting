/**
 * Internal dependencies
 */
/**
 * Internal dependencies
 */

import {
	RECONCILIATIONS_LOADED,
	RECONCILIATIONS_LOADING,
	RECONCILIATIONS_FAILED,
	RECONCILIATIONS_SET_SELECTED,
	RECONCILIATIONS_SET_ALL_SELECTED,
	RECONCILIATIONS_ITEM_SAVING,
	RECONCILIATIONS_ITEM_SAVED,
	RECONCILIATIONS_ITEM_ADDED,
	RECONCILIATIONS_ITEM_FAILED,
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
		case RECONCILIATIONS_LOADING:
			return {...state, table: setTable(state, action), status: STATUS_IN_PROGRESS, saving: setSaving(state, action)};

		case RECONCILIATIONS_LOADED:
			return {
				...state,
				rows: action.data,
				status: STATUS_COMPLETE,
				total: setTotal(state, action),
				table: clearSelected(state.table)
			};

		case RECONCILIATIONS_ITEM_SAVING:
			return {
				...state,
				table: clearSelected(setTable(state, action)),
				saving: setSaving(state, action),
				rows: setUpdatedItem(state.rows, action)
			};

		case RECONCILIATIONS_ITEM_SAVED:
			return {...state, saving: removeSaving(state, action)};

		case RECONCILIATIONS_ITEM_ADDED:
			return {...state, rows: [action.item, ...state.rows], total: state.total + 1};

		case RECONCILIATIONS_SET_ALL_SELECTED:
			return {...state, table: setTableAllSelected(state.table, state.rows, action.onoff)};

		case RECONCILIATIONS_SET_SELECTED:
			return {...state, table: setTableSelected(state.table, action.items)};

		case RECONCILIATIONS_FAILED:
			return {...state, status: STATUS_FAILED, saving: []};

		case RECONCILIATIONS_ITEM_FAILED:
			return {...state, saving: removeSaving(state, action), rows: restoreToOriginal(state, action)};
	}

	return state;
}
