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

export default function accounts(state = {}, action) {
	switch (action.type) {
		case TRANSACTIONS_LOADING:
			return {
				...state,
				table: setTable(state, action),
				status: STATUS_IN_PROGRESS,
				saving: setSaving(state, action)
			};

		case TRANSACTIONS_LOADED:
			return {
				...state,
				rows: action.data,
				status: STATUS_COMPLETE,
				total: setTotal(state, action),
				table: clearSelected(state.table)
			};
	}

	return state;
}
