/**
 * Set isRequesting
 *
 * @param isRequesting
 * @return {{type: string, isRequesting}}
 */
/**
 * Internal dependencies
 */
import { apiFetch } from '../base-controls';
import { API_NAMESPACE } from '../localized-data';

/**
 * Receive settings.
 *
 * @param {Object} settings
 * @param {Object} error
 * @param {Object} time
 * @return {{settings, time: Date, type: string, error: null}} Settings update action.
 */
export function receiveSettings( settings, error = null, time = new Date() ) {
	return {
		type: 'RECEIVE_SETTINGS',
		settings,
		error,
		time,
	};
}

/**
 * Update options
 *
 * @param {Object} data
 * @return {Generator<*&{success: boolean}, void, ?>} Resolver.
 */
export function* updateSettings( data ) {
	try {
		const settings = yield apiFetch( {
			path: API_NAMESPACE + '/settings',
			method: 'POST',
			data,
		} );
		yield receiveSettings( settings );
		return { success: true, ...settings };
	} catch ( error ) {
		yield receiveSettings( {}, error );
		return { success: false, ...error };
	}
}

/**
 * Update option.
 *
 * @param {number} id
 * @param {string} value
 * @return {Generator<*&{success: boolean}, void, ?>} Resolver.
 */
export function* updateOption( id, value ) {
	try {
		const settings = yield apiFetch( {
			path: API_NAMESPACE + '/settings/' + id,
			method: 'PUT',
			data: { id, value },
		} );

		yield receiveSettings( { settings: [ settings ] } );
		return { success: true, ...settings };
	} catch ( error ) {
		yield receiveSettings( {}, error );
		return { success: false, ...error };
	}
}
