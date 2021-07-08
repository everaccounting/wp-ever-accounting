/**
 * Returns an action used in signalling that the current user has been received.
 *
 * @param {Object} currentUser Current user object.
 *
 * @return {Object} Action object.
 */
export function receiveCurrentUser( currentUser ) {
	return {
		type: 'RECEIVE_CURRENT_USER',
		currentUser,
	};
}

/**
 * Returns an action object used in signalling that the current user has
 * permission to perform an action on a REST resource.
 *
 * @param {string}  key       A key that represents the action and REST resource.
 * @param {boolean} isAllowed Whether or not the user can perform the action.
 *
 * @return {Object} Action object.
 */
export function receiveUserPermission( key, isAllowed ) {
	return {
		type: 'RECEIVE_USER_PERMISSION',
		key,
		isAllowed,
	};
}
