/**
 * Set isRequesting
 *
 * @param isRequesting
 * @returns {{type: string, isRequesting}}
 */
import {fetch} from "../controls";
import {API_NAMESPACE} from "../constants";

export function setIsRequesting(isRequesting) {
	return {
		type: 'SET_IS_REQUESTING',
		isRequesting,
	};
}

/**
 *
 * @param settings
 * @param time
 * @returns {{settings, type: string}}
 */
export function receiveSettings(settings, time = new Date()) {
	return {
		type: 'RECEIVE_SETTINGS',
		settings,
		time
	};
}

/**
 *
 * @param error
 * @param time
 * @returns {{time: Date, type: string, error}}
 */
export function receiveSettingsError(error, time = new Date()) {
	return {
		type: 'RECEIVE_SETTINGS_ERROR',
		error,
		time
	};
}

/**
 * Update options
 * @param settings
 * @returns {Generator<{settings, type: string}|{time: Date, type: string, error}|{type: string, request: Object}, *&{success: boolean}, *>}
 */
export function* updateOptions(settings) {
	try {
		const settings = yield fetch({
			path: API_NAMESPACE + '/settings',
			method: 'POST',
			data: settings,
		});
		yield receiveSettings(settings);
		return {success: true, ...settings};
	} catch (error) {
		yield receiveSettingsError(error);
		return {success: false, ...error};
	}
}

/**
 * Update option.
 *
 * @param id
 * @param value
 * @returns {Generator<*&{success: boolean}, void, ?>}
 */
export function* updateOption(id, value) {
	try {
		const settings = yield fetch({
			path: API_NAMESPACE + '/settings/' + id,
			method: 'PUT',
			data: {id, value},
		});

		yield receiveSettings([settings]);
		return {success: true, ...settings};
	} catch (error) {
		yield receiveSettingsError(error);
		return {success: false, ...error};
	}
}
