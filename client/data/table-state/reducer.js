import {ACTION_TYPES as types} from './action-types';
import {hasInState, updateState} from '../utils';
import {has} from "lodash";



export const reducer = (state = {}, action = {}) => {
	const {resourceName} = action;
	switch (action.type) {
		case types.RECEIVE_SELECTED:
			state = updateState(state, [resourceName, 'selected'], hasInState(state, [resourceName, 'selected'])? xor(table.selected, ids): [action.id] );
			break;
		case types.RECEIVE_ALL_SELECTED:
			state = updateState(state, [resourceName, 'selected'], action.items.map(item => item.id));
			break;
		case types.RECEIVE_TOTAL:
			state = updateState(state, [resourceName, 'total'], !isNaN(action.total) ? action.total : state[resourceName]['total']);
			break;
	}
	return state;
};
