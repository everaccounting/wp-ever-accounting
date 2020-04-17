import {ACTION_TYPES as types} from "./action-types";
import {combineReducers} from '@wordpress/data';
import {xor} from 'lodash';

const DEFAULT_TABLE_STATE = {
	rows: [],
	selected: [],
	total: 0,
	status: types.TABLE_LOADING,
};

/**
 * Receive table
 *
 * @param state
 * @param action
 * @returns {{total: number, rows: [], selected: [], status: *}|{total: number, rows: [], selected: [], status: string}|{total: *, rows: *, selected: [], status: string}|{total: number, rows: [], selected: [], status: *}}
 */
export const receiveTable = (state = DEFAULT_TABLE_STATE, action) => {
	const {type, payload} = action;
	switch (type) {
		case types.TABLE_LOADING:
			return {...state, status: "STATUS_IN_PROGRESS"};
		case types.TABLE_FAILED:
			return {...state, status: "STATUS_FAILED"};
		case types.TABLE_LOADED:
			return {
				...state,
				rows: payload.rows,
				total: !isNaN(payload.total) ? payload.total : state.total,
				status: "STATUS_COMPLETE"
			};
		case types.TABLE_RESET:
			return {...state, ...DEFAULT_TABLE_STATE};
		case types.TABLE_ITEM_SELECTED:
			return {...state, selected: xor(state.selected, [payload.id])};
		case types.TABLE_ALL_SELECTED:
			return {...state, selected: payload.onoff ? state.rows.map(item => item.id) : []};
		default:
			return state;
	}
};

/**
 *
 * @param state
 * @param action
 * @returns {{}}
 */
export const receiveSettings = (state = {}, action) => {
	return state;
};

/**
 *
 * @param state
 * @param action
 * @returns {{}}
 */
export const receiveEntities = (state = {}, action) => {
	return state;
};


/**
 *
 * @param state
 * @param action
 * @returns {{}}
 */
export const receiveCollection = (state = {}, action) => {
	return state;
};

/**
 *
 * @param state
 * @param action
 * @returns {{}}
 */
export const receiveForm = (state = {}, action) => {
	return state;
};


/**
 *
 * @param state
 * @param action
 * @returns {{}}
 */

const DEFAULT_QUERY_STATE = {
	per_page: 50,
	order: 'DESC',
	orderby: 'id',
	page: 1,
};

export const receiveQuery = (state = DEFAULT_QUERY_STATE, action) => {
	const {type, payload} = action;
	switch (type) {
		case types.SET_QUERY:
			return {...state, ...payload.query};
		case types.SET_QUERIES:
			return {...state, ...payload.queries};
		case types.RESET_QUERIES:
			return {...state, ...DEFAULT_QUERY_STATE};
		default:
			return state;
	}
};


export default combineReducers({
	table: receiveTable,
	settings: receiveSettings,
	entities: receiveEntities,
	collection: receiveCollection,
	form: receiveForm,
	query: receiveQuery,
});
