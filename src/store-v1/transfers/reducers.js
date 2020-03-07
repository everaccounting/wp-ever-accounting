import { initialTransfers } from './index';
import { STATUS_IN_PROGRESS, STATUS_COMPLETE, STATUS_FAILED } from 'status';
import {setTable, setSaving, setTableAllSelected, setTableSelected, setUpdatedItem} from '../util';

const transfers = (state = initialTransfers, action) => {
	switch (action.type) {
		case 'TRANSFERS_LOADING':
			return { ...state, table: setTable(state, action), status: STATUS_IN_PROGRESS, saving: setSaving(state, action) };

		case 'TRANSFERS_SUCCESS':
			return {
				...state,
				status: STATUS_COMPLETE,
				rows: action.payload.data,
				total: action.payload.total || state.total,
				table: { ...state.table, selected: [] },
			};

		case 'TRANSFERS_FAILED':
			return { ...state, status: STATUS_FAILED };

		case 'TRANSFERS_ALL_SELECTED':
			return { ...state, table: setTableAllSelected(state.table, state.rows, action.payload) };

		case 'TRANSFERS_SELECTED':
			return { ...state, table: setTableSelected(state.table, action.ids) };

		case 'TRANSFERS_UPDATED':
			return { ...state, rows: setUpdatedItem(state.rows, action) };

		case 'TRANSFERS_ADDED':
			return { ...state, rows: [action.item, ...state.rows]};
		default:
			return state;
	}
};

export default transfers;
