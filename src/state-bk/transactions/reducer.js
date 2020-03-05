/**
 * Internal dependencies
 */

import { TRANSACTIONS_LOADED, TRANSACTIONS_LOADING } from './type';
import { STATUS_IN_PROGRESS, STATUS_COMPLETE } from 'status';
import { setTable, setTotal, setSaving, clearSelected } from 'lib/table';

export default function accounts(state = {}, action) {
	switch (action.type) {
		case TRANSACTIONS_LOADING:
			return {
				...state,
				table: setTable(state, action),
				status: STATUS_IN_PROGRESS,
				saving: setSaving(state, action),
			};

		case TRANSACTIONS_LOADED:
			return {
				...state,
				rows: action.data,
				status: STATUS_COMPLETE,
				total: setTotal(state, action),
				table: clearSelected(state.table),
			};
	}

	return state;
}
