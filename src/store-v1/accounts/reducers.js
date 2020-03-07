import { initialAccounts } from './index';
import { STATUS_IN_PROGRESS, STATUS_COMPLETE, STATUS_FAILED } from 'status';
import {setTable, setSaving, setTableAllSelected, setTableSelected, setUpdatedItem} from '../util';

const revenues = (state = initialAccounts, action) => {
	switch (action.type) {
		case 'ACCOUNTS_LOADING':
			return { ...state, table: setTable(state, action), status: STATUS_IN_PROGRESS, saving: setSaving(state, action) };

		case 'ACCOUNTS_SUCCESS':
			return {
				...state,
				status: STATUS_COMPLETE,
				rows: action.payload.data,
				total: action.payload.total || state.total,
				table: { ...state.table, selected: [] },
			};

		case 'ACCOUNTS_FAILED':
			return { ...state, status: STATUS_FAILED };

		case 'ACCOUNTS_UPDATED':
			return { ...state, rows: setUpdatedItem(state.rows, action) };

		case 'ACCOUNTS_ADDED':
			return { ...state, rows: [action.item, ...state.rows]};

		default:
			return state;
	}
};

export default revenues;
