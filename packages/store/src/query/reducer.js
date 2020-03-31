import {ACTION_TYPES as types} from "./action-types";
import {isEmpty} from "lodash";

/**
 * Reducer for processing actions related to the query state store.
 *
 * @param {Object} state  Current state in store.
 * @param {Object} action Action being processed.
 */
const reducer = (state = {}, action) => {
	const {type, context, queryKey, value } = action;
	const prevState = typeof state[context] === 'undefined' ? null : state[context];
	let newState;
	switch (type) {
		case types.SET_QUERY:
			const prevStateObject = prevState !== null ? JSON.parse(prevState) : {};
			prevStateObject[queryKey] = value;
			newState = JSON.stringify(prevStateObject);
			if (prevState !== newState) {
				state = {...state, [context]: newState};
			}
			break;
		case types.SET_CONTEXT_QUERY:
			newState = JSON.stringify(value);
			if (prevState !== newState) {
				state = {...state, [context]: newState};
			}
			break;

		case types.RESET_CONTEXT_QUERY:
			state = {...state, [context]: null};
	}

	return state;
};

export default reducer;
