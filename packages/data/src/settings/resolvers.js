/**
 * WordPress dependencies
 */
import apiFetch from '@wordpress/api-fetch';
import { addQueryArgs } from '@wordpress/url';

/**
 * Internal dependencies
 */
import { NAMESPACE } from '../constants';
import { forwardResolver } from '../utils';

/**
 * Get All Settings.
 * @return {Object} Setting object
 */
export const getSettings =
	() =>
	async ( { dispatch } ) => {
		try {
			const path = addQueryArgs( `${ NAMESPACE }/settings` );
			const response = await apiFetch( { path } );
			const settings = response.reduce( ( memo, setting ) => {
				return [
					...memo,
					{
						...setting,
						value: setting.value ?? setting.default,
						error: null,
						lastReceived: Date.now(),
						isRequesting: false,
						dirty: false,
					},
				];
			}, [] );

			dispatch.receiveSettings( settings );
		} catch ( error ) {
			throw error;
		}
	};

/**
 * Get a single setting.
 *
 * @param {string} name The setting name.
 *
 * @return {Object} Setting object
 */
export const getSetting = forwardResolver( 'getSettings' );

/**
 * Get All Settings data.
 *
 * @param {Object} state Global state tree
 *
 * @return {Object} Setting object
 */
export const getSettingsData = forwardResolver( 'getSettings' );

/**
 * Get a single setting value from the settings store.
 *
 * @param {Object} state Global state tree
 * @param {string} name  Setting name to retrieve
 *
 * @return {*} Setting option
 */
export const getSettingData = forwardResolver( 'getSettings' );
