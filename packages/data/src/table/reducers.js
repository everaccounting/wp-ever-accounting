import {ACTION_TYPES as types} from './action-types';
import {xor} from "lodash";

const reducers = (state = {}, action) => {
	switch (action.type) {
		case types.TABLE_ITEMS_LOADED:
			state = {
				...state,
				items: action.items,
				total: action.headers.get('x-wp-total') || state.total,
				table: { ...state.table, selected: [] },
				status: "STATUS_COMPLETE",
			};
			break;
		case types.TABLE_ITEM_SELECTED:
			state = {
				...state,
				table:{...state.table, selected: xor(state.table.selected, [action.id])}
			};
			break;
		case types.TABLE_ALL_SELECTED:
			state = {
				...state,
				table:{...state.table, selected: action.onoff ? state.rows.map(item => item.id) : []}
			};
			break;
		case types.TABLE_FAILED:
		state = {
			...state,
			status: "STATUS_FAILED",
		};
		break;

	}
	return state;
};

export default reducers;
