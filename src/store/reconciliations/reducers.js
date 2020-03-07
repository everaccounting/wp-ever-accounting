import { initialReconciliations } from './index';
import { STATUS_IN_PROGRESS, STATUS_COMPLETE, STATUS_FAILED } from 'status';
import {setTable, setSaving, setTableAllSelected, setTableSelected, setUpdatedItem} from '../util';

const revenues = (state = initialReconciliations, action) => {
	switch (action.type) {
		case 'RECONCILIATIONS_LOADING':
			return { ...state, table: setTable(state, action), status: STATUS_IN_PROGRESS, saving: setSaving(state, action) };

		case 'RECONCILIATIONS_SUCCESS':
			return {
				...state,
				status: STATUS_COMPLETE,
				rows: action.payload.data,
				total: action.payload.total || state.total,
				table: { ...state.table, selected: [] },
			};

		case 'RECONCILIATIONS_FAILED':
			return { ...state, status: STATUS_FAILED };

		case 'RECONCILIATIONS_UPDATED':
			return { ...state, rows: setUpdatedItem(state.rows, action) };

		case 'RECONCILIATIONS_ADDED':
			return { ...state, rows: [action.item, ...state.rows]};

		default:
			return state;
	}
};

export default revenues;
