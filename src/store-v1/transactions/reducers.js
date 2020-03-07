import { initialTransactions } from './index';
import { STATUS_IN_PROGRESS, STATUS_COMPLETE, STATUS_FAILED } from 'status';
import { setTable, setSaving } from '../util';

const revenues = (state = initialTransactions, action) => {
	switch (action.type) {
		case 'TRANSACTIONS_LOADING':
			return { ...state, table: setTable(state, action), status: STATUS_IN_PROGRESS, saving: setSaving(state, action) };

		case 'TRANSACTIONS_SUCCESS':
			return {
				...state,
				status: STATUS_COMPLETE,
				rows: action.payload.data,
				total: action.payload.total || state.total,
				table: { ...state.table, selected: [] },
			};

		case 'TRANSACTIONS_FAILED':
			return { ...state, status: STATUS_FAILED };

		default:
			return state;
	}
};

export default revenues;
