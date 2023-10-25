/**
 * External dependencies
 */
import { get } from 'lodash';

/**
 * Get All Settings.
 *
 * @param {Object} state Global state tree
 *
 * @return {Object} Setting object
 */
export function getSettings( state ) {
	return Object.keys( state ).reduce( ( acc, name ) => {
		return {
			...acc,
			[ name ]: state[ name ].value,
		};
	}, {} );
}

/**
 * Get a single setting value from the settings store.
 *
 * @param {Object} state Global state tree
 * @param {string} name  Setting name to retrieve
 *
 * @return {*} Setting option
 */
export function getSetting( state, name ) {
	return get( state, [ name, 'value' ], null );
}

/**
 * Get dirty settings.
 *
 * @param {Object} state Global state tree
 * @return {Array} Array of dirty settings
 */
export function getDirtySettings( state ) {
	return Object.keys( state )
		.filter( ( name ) => state[ name ].dirty )
		.reduce( ( acc, name ) => {
			return {
				...acc,
				[ name ]: state[ name ].update,
			};
		}, {} );
}

/**
 * Get All Settings data.
 *
 * @param {Object} state Global state tree
 *
 * @return {Object} Setting object
 */
export function getSettingsData( state ) {
	return state;
}

/**
 * Get a single setting value from the settings store.
 *
 * @param {Object} state Global state tree
 * @param {string} name  Setting name to retrieve
 *
 * @return {*} Setting option
 */
export function getSettingData( state, name ) {
	return get( state, [ name ], null );
}
