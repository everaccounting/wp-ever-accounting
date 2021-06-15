import {compact, get} from "lodash";

/**
 * Returns whether the current user can perform the given action on the given
 * REST resource.
 *
 * Calling this may trigger an OPTIONS request to the REST API via the
 * `canUser()` resolver.
 *
 * https://developer.wordpress.org/rest-api/reference/
 *
 * @param {Object}   state            Data state.
 * @param {string}   action           Action to check. One of: 'create', 'read', 'update', 'delete'.
 * @param {string}   resource         REST resource to check, e.g. 'media' or 'posts'.
 * @param {string=}  id               Optional ID of the rest resource to check.
 *
 * @return {boolean|undefined} Whether or not the user can perform the action,
 *                             or `undefined` if the OPTIONS request is still being made.
 */
export function canUser( state, action, resource, id ) {
	const key = compact( [ action, resource, id ] ).join( '/' );
	return get( state, [ 'userPermissions', key ] );
}

/**
 * Returns the current user.
 *
 * @param {Object} state Data state.
 *
 * @return {Object} Current user object.
 */
export function getCurrentUser( state ) {
	return state.currentUser;
}
