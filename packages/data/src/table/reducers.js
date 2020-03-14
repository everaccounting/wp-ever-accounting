import {ACTION_TYPES as types} from './action-types';
import {xor} from "lodash";

const reducers = (state = {}, action) => {
	switch (action.type) {
		case types.TABLE_LOADING:
			state = {
				...state,
				status: "STATUS_IN_PROGRESS",
			};
			break;
		case types.TABLE_ITEMS_LOADED:
			state = {
				...state,
				items: action.items,
				total: parseInt(action.headers.get('x-wp-total'), 10) || parseInt(state.total, 10),
				table: {...state.table, ...action.table, selected: []},
				status: "STATUS_COMPLETE",
				error: ''
			};
			break;
		case types.TABLE_ITEM_SELECTED:
			state = {
				...state,
				table: {...state.table, selected: xor(state.table.selected, [action.id])}
			};
			break;
		case types.TABLE_PAGE_CHANGED:
			state = {
				...state,
				table: {...state.table, page: action.page}
			};
			break;
		case types.TABLE_ALL_SELECTED:
			state = {
				...state,
				table: {...state.table, selected: action.onoff ? state.items.map(item => item.id) : []}
			};
			break;
		case types.TABLE_FAILED:
			state = {
				...state,
				status: "STATUS_FAILED",
				error: action.error
			};
			break;

	}
	return state;
};

export default reducers;
