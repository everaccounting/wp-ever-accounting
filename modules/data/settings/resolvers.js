import {fetch, resolveSelect, dispatch, select} from '../controls';
import {API_NAMESPACE} from '../constants'
import {receiveSettings, receiveSettingsError} from './actions';
import {STORE_NAME} from "./constants";

/**
 * Get settings.
 *
 * @returns {Generator<{settings, type: string}|{args: Array[], reducerKey: string, selectorName: string, type: string}|{type: string, reducerKey: string, dispatchName: string, args: *[]}|{type: string, request: Object}, {time: Date, type: string, error}|*, Generator<{type: string, reducerKey: string, dispatchName: string, args: *[]}|{type: string, request: Object}|{settings, type: string}|{args: Array[], reducerKey: string, selectorName: string, type: string}, *|{time: Date, type: string, error}, *>>}
 */
export function* getOptions(){
	yield dispatch( STORE_NAME, 'setIsRequesting', true );
	try {
		const path = `${API_NAMESPACE}/settings`
		const  settings = yield fetch({path});
		yield receiveSettings(settings);
		return yield resolveSelect(STORE_NAME, 'getOptions' );
	}catch (error) {
		return receiveSettingsError(error)
	}
}

/**
 * Get option.
 *
 * @param state
 * @param name
 * @param fallback
 * @param filter
 * @returns {Generator<{args: Array[], reducerKey: string, selectorName: string, type: string}|{type: string, reducerKey: string, selectorName: string, args: *[]}, *, Generator<{args: Array[], reducerKey: string, selectorName: string, type: string}|{type: string, reducerKey: string, selectorName: string, args: *[]}, *, *>>}
 */
export function* getOption( state, name, fallback = false, filter = (val) => val ){
	yield resolveSelect(STORE_NAME, 'getOptions');
	return yield select(STORE_NAME, 'getOption', name, fallback, filter);
}
