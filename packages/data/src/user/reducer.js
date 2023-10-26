/**
 * Reducer managing current user state.
 *
 * @param {Object} state  Current state.
 * @param {Object} action Dispatched action.
 *
 * @return {Object} Updated state.
 */
export function reducer( state = {}, action ) {
	switch ( action.type ) {
		case 'RECEIVE_CURRENT_USER':
			return action.currentUser;
		case 'RECEIVE_USER_PERMISSION':
			return {
				...state,
				permissions: { [ action.key ]: action.isAllowed },
			};
	}

	return state;
}

export default reducer;
