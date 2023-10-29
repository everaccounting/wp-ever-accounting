/**
 * WordPress dependencies
 */
import apiFetch from '@wordpress/api-fetch';
/**
 * Internal dependencies
 */
import { NAMESPACE } from '../constants';

/**
 * Returns an action object used in signalling that settings groups have been received.
 *
 * @param {Object} settings Array of settings.
 * @param {Date}   time     Time when the settings were received.
 *
 * @return {Object} Action object.
 */
export function receiveSettings( settings, time = new Date() ) {
	return {
		type: 'RECEIVE_SETTINGS',
		settings: Array.isArray( settings ) ? settings : [ settings ],
		time,
	};
}

/**
 * Returns an action object used in signalling that settings are being requested.
 *
 * @param {Array} names Array of settings names.
 * @param {Date}  time  Time when the settings were requested.
 *
 * @return {Object} Action object.
 */
export function setRequestingSettings( names, time = new Date() ) {
	return {
		type: 'SET_REQUESTING_SETTINGS',
		settings: Array.isArray( names ) ? names : [ names ],
		time,
	};
}

/**
 * Returns an action object used in signalling that settings received an error.
 *
 * @param {Object} errors Errors for settings groups.
 * @param {Date}   time   Time when the settings groups were received.
 *
 * @return {Object} Action object.
 */
export function receiveSettingsError( errors, time = new Date() ) {
	return {
		type: 'RECEIVE_SETTINGS_ERROR',
		errors,
		time,
	};
}

/**
 * Action triggered to edit an option.
 *
 * @param {string} name  Option name.
 * @param {*}      value Option value.
 *
 * @return {Object} Action object.
 */
export const editOption =
	( name, value ) =>
	async ( { dispatch, resolveSelect } ) => {
		try {
			const settings = await resolveSelect.getSettings();
			const setting = Object.keys( settings ).find( ( single ) => single === name );
			if ( ! setting ) {
				return;
			}
			await dispatch(
				receiveSettings( [
					{
						...settings[ name ],
						update: value,
						dirty: true,
						error: null,
					},
				] )
			);
		} catch ( error ) {
			throw error;
		}
	};

/**
 * Action triggered to save settings.
 *
 * @param {Object} options Settings to save.
 *
 * @return {Object} Action object.
 */
export const saveOptions =
	( options = {} ) =>
	async ( { dispatch, resolveSelect } ) => {
		try {
			await resolveSelect.getSettings();
			// loop through the options and call editOption for each option.
			for ( const [ name, value ] of Object.entries( options ) ) {
				await dispatch( editOption( name, value ) );
			}
			const settings = await resolveSelect.getDirtyOptions();
			if ( ! settings ) {
				return;
			}
			const response = await apiFetch( {
				path: `${ NAMESPACE }/settings`,
				method: 'POST',
				data: settings,
			} );
			await dispatch( receiveSettings( response ) );
		} catch ( error ) {
			// if error has data.params then set the error for the setting.
			if ( error?.data?.params ) {
				await dispatch( receiveSettingsError( error.data.params ) );
			}
			throw error;
		}
	};
