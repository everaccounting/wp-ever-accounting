/**
 * Internal dependencies
 */
import { apiFetch, resolveSelect, select } from '../base-controls';
import { receiveSettings } from './actions';
import { API_NAMESPACE } from '../localized-data';
import { STORE_NAME } from './constants';
import { SCHEMA_STORE_NAME } from '../schema';

/**
 * Retrieves options value from the options store.
 */
export function* getSettings() {
	try {
		const path = yield select( SCHEMA_STORE_NAME, 'getRoute', 'settings' );
		const settings = yield apiFetch( { path } );
		yield receiveSettings( settings );
	} catch ( error ) {
		receiveSettings( {}, error );
	}
}

/**
 * Retrieves an option value from the options store.
 *
 * @param {string}   name   The identifier for the setting.
 * @param {*}    [fallback=false]  The value to use as a fallback if the setting is not in the state.
 * @param {Function} [filter=( val ) => val]  A callback for filtering the value before it's returned. Receives both the found value (if it exists for the key) and the provided fallback arg.
 **/
export function* getOption( name, fallback = false, filter = ( val ) => val ) {
	yield resolveSelect( STORE_NAME, 'getSettings' );
	yield select( STORE_NAME, 'getOption', name, fallback, filter );
}
