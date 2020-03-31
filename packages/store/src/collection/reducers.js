/**
 * Internal dependencies
 */
import {ACTION_TYPES as types} from './action-types';
import {hasInState, updateState} from "../utils";
/**
 * External dependencies
 */

const DEFAULT_LISTS_STATE = {};

/**
 * Reducer managing item list state.
 *
 * @param {Immutable.Map} state  Current state.
 * @param {Object} action	Dispatched action.
 * @return {Immutable.Map}	Updated state.
 */
export function receiveCollection(state = DEFAULT_LISTS_STATE, action) {
	let {type, resourceName, queryString,  response} = action;
	const ids = action.ids ? JSON.stringify(action.ids) : '[]';
	switch (type) {
		case types.RECEIVE_COLLECTION:
			state = updateState(state, [resourceName, ids, queryString], response);
			break;
		case types.RESET_COLLECTION:
			state = updateState(state, [resourceName, ids, queryString], {});
			break;
		case types.COLLECTION_ERROR:
			state = updateState(state, [resourceName, ids, queryString], response);
			break;
	}
	return state;
}

export default receiveCollection;
