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
 * @param {Object} options Settings to edit.
 *
 * @return {Object} Action object.
 */
export const editSettings =
	( options = {} ) =>
	async ( { dispatch, resolveSelect } ) => {
		try {
			// if settings is not an object or array then throw error.
			const settings = await resolveSelect.getSettings();
			const editedSettings = Object.keys( options ).reduce( ( memo, name ) => {
				if ( settings[ name ] && settings[ name ].value !== options[ name ] ) {
					return [
						...memo,
						{
							name,
							...settings[ name ],
							update: options[ name ],
							dirty: true,
							error: null,
						},
					];
				}
				return memo;
			}, [] );
			await dispatch( receiveSettings( editedSettings ) );
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
export const saveSettings =
	( options = {} ) =>
	async ( { dispatch, resolveSelect } ) => {
		try {
			await dispatch( editSettings( options ) );
			const dirtySettings = await resolveSelect.getDirtySettings();
			if ( ! dirtySettings ) {
				return;
			}
			await dispatch( setRequestingSettings( Object.keys( dirtySettings ) ) );
			const response = await apiFetch( {
				path: `${ NAMESPACE }/settings`,
				method: 'POST',
				data: dirtySettings,
			} );
			await dispatch( receiveSettings( response ) );
			// now update the settings.
		} catch ( error ) {
			// if error has data.params then set the error for the setting.
			if ( error?.data?.params ) {
				await dispatch( receiveSettingsError( error.data.params ) );
			}
			throw error;
		}
	};
