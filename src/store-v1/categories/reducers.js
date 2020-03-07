import { initialCategories } from './index';
import { STATUS_IN_PROGRESS, STATUS_COMPLETE, STATUS_FAILED } from 'status';
import { setTable, setSaving, setTableAllSelected, setTableSelected, setUpdatedItem } from '../util';

const revenues = (state = initialCategories, action) => {
	switch (action.type) {
		case 'CATEGORIES_LOADING':
			return { ...state, table: setTable(state, action), status: STATUS_IN_PROGRESS, saving: setSaving(state, action) };

		case 'CATEGORIES_SUCCESS':
			return {
				...state,
				status: STATUS_COMPLETE,
				rows: action.payload.data,
				total: action.payload.total || state.total,
				table: { ...state.table, selected: [] },
			};

		case 'CATEGORIES_FAILED':
			return { ...state, status: STATUS_FAILED };

		case 'CATEGORIES_ALL_SELECTED':
			return { ...state, table: setTableAllSelected(state.table, state.rows, action.payload) };

		case 'CATEGORIES_SELECTED':
			return { ...state, table: setTableSelected(state.table, action.ids) };

		case 'CATEGORIES_UPDATED':
			return { ...state, rows: setUpdatedItem(state.rows, action) };

		case 'CATEGORIES_ADDED':
			return { ...state, rows: [action.item, ...state.rows]};
		default:
			return state;
	}
};

export default revenues;
