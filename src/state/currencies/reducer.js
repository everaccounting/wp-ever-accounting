/**
 * Internal dependencies
 */

import {
	CURRENCIES_LOADED,
	CURRENCIES_LOADING,
	CURRENCIES_FAILED,
	CURRENCIES_SET_SELECTED,
	CURRENCIES_SET_ALL_SELECTED,
	CURRENCIES_ITEM_SAVING,
	CURRENCIES_ITEM_SAVED,
	CURRENCIES_ITEM_ADDED,
	CURRENCIES_ITEM_FAILED,
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

export default function currencies(state = {}, action) {
	switch (action.type) {
		case CURRENCIES_LOADING:
			return {...state, table: setTable(state, action), status: STATUS_IN_PROGRESS, saving: setSaving(state, action)};

		case CURRENCIES_LOADED:
			return {
				...state,
				rows: action.data,
				status: STATUS_COMPLETE,
				total: setTotal(state, action),
				table: clearSelected(state.table)
			};

		case CURRENCIES_ITEM_SAVING:
			return {
				...state,
				table: clearSelected(setTable(state, action)),
				saving: setSaving(state, action),
				rows: setUpdatedItem(state.rows, action)
			};

		case CURRENCIES_ITEM_SAVED:
			return {...state, saving: removeSaving(state, action)};

		case CURRENCIES_ITEM_ADDED:
			return {...state, rows: [action.item, ...state.rows], total: state.total + 1};

		case CURRENCIES_SET_ALL_SELECTED:
			return {...state, table: setTableAllSelected(state.table, state.rows, action.onoff)};

		case CURRENCIES_SET_SELECTED:
			return {...state, table: setTableSelected(state.table, action.items)};

		case CURRENCIES_FAILED:
			return {...state, status: STATUS_FAILED, saving: []};

		case CURRENCIES_ITEM_FAILED:
			return {...state, saving: removeSaving(state, action), rows: restoreToOriginal(state, action)};
	}

	return state;
}
