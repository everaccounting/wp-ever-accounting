/**
 * External dependencies
 */
/**
 * WordPress dependencies
 */
import { apiFetch } from '@wordpress/data-controls';
/**
 * Internal dependencies
 */
import TYPES from './action-types';
import { NAMESPACE } from '../constants';
export function receiveOptions( options ) {
	return {
		type: TYPES.RECEIVE_OPTIONS,
		options,
	};
}
export function setRequestingError( error, name ) {
	return {
		type: TYPES.SET_REQUESTING_ERROR,
		error,
		name,
	};
}
export function setUpdatingError( error ) {
	return {
		type: TYPES.SET_UPDATING_ERROR,
		error,
	};
}
export function setIsUpdating( isUpdating ) {
	return {
		type: TYPES.SET_IS_UPDATING,
		isUpdating,
	};
}
export function* updateOptions( data ) {
	yield setIsUpdating( true );
	yield receiveOptions( data );
	try {
		const results = yield apiFetch( {
			path: NAMESPACE + '/options',
			method: 'POST',
			data,
		} );
		yield setIsUpdating( false );
		if ( typeof results !== 'object' ) {
			throw new Error( `Invalid update options response from server: ${ results }` );
		}
		return { success: true, ...results };
	} catch ( error ) {
		yield setUpdatingError( error );
		if ( typeof error !== 'object' ) {
			throw new Error( `Unexpected error: ${ error }` );
		}
		return { success: false, ...error };
	}
}
