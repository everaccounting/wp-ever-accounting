/**
 * WordPress dependencies
 */
import apiFetch from '@wordpress/api-fetch';

/**
 * Requests the current user from the REST API.
 */
export const getCurrentUser =
	() =>
	async ( { dispatch } ) => {
		const currentUser = await apiFetch( { path: '/wp/v2/users/me?context=edit' } );
		await dispatch.receiveCurrentUser( currentUser );
	};
