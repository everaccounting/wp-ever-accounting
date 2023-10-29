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
			[ name ]: state[ name ],
		};
	}, {} );
}

/**
 * Get a single setting from the settings store.
 *
 * @param {Object} state Global state tree
 * @param {string} name  Setting name to retrieve
 *
 * @return {*} Setting option
 */
export function getSetting( state, name ) {
	return get( state, [ name ], null );
}

/**
 * Get a all settings value from the settings store.
 *
 * @param {Object} state Global state tree
 *
 * @return {*} Setting option
 */
export function getOptions( state ) {
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
export function getOption( state, name ) {
	return state?.[ name ]?.value ?? null;
}

/**
 * Get all dirty settings from the settings store.
 *
 * @param {Object} state Global state tree
 *
 * @return {*} Setting option
 */
export function getDirtyOptions( state ) {
	return Object.keys( state ).reduce( ( acc, name ) => {
		if ( state[ name ].dirty ) {
			return {
				...acc,
				[ name ]: state[ name ].update,
			};
		}
		return acc;
	}, {} );
}

/**
 * Get if any settings are dirty.
 *
 * @param {Object} state Global state tree
 *
 * @return {boolean} True if any settings are dirty.
 */
export function hasDirtySettings( state ) {
	return Object.keys( state ).some( ( name ) => state[ name ].dirty );
}
