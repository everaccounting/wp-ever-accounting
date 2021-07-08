/**
 * WordPress dependencies
 */
import { combineReducers } from '@wordpress/data';

/**
 * State which tracks whether the user can perform an action on a REST
 * resource.
 *
 * @param  {Object} state  Current state.
 * @param  {Object} action Dispatched action.
 *
 * @return {Object} Updated state.
 */
export function userPermissions( state = {}, action ) {
	switch ( action.type ) {
		case 'RECEIVE_USER_PERMISSION':
			return {
				...state,
				[ action.key ]: action.isAllowed,
			};
	}

	return state;
}

/**
 * Reducer managing current user state.
 *
 * @param {Object} state  Current state.
 * @param {Object} action Dispatched action.
 *
 * @return {Object} Updated state.
 */
export function currentUser( state = {}, action ) {
	switch ( action.type ) {
		case 'RECEIVE_CURRENT_USER':
			return action.currentUser;
	}

	return state;
}

export default combineReducers( {
	currentUser,
	userPermissions,
} );
