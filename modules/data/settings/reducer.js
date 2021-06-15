import {isEmpty} from "lodash";
import {combineReducers} from '@wordpress/data';

/**
 * State for storing settings options.
 *
 * @param  {Object} state  Current state.
 * @param  {Object} action Dispatched action.
 *
 * @return {Object} Updated state.
 */

export function options(state = {}, action) {
	const {settings = [], type, error, time, isRequesting} = action;
	switch (type) {
		case 'SET_IS_REQUESTING':
			state = {
				...state,
				isRequesting,
			};
			break;
		case 'RECEIVE_SETTINGS_ERROR':
		case 'RECEIVE_SETTINGS':
			state = {
				...state,
				...settings.reduce((result, setting) => {
					return {
						...result,
						[setting.id]: isEmpty(setting.value) && setting.default ? setting.default : setting.value
					};
				}, {}),
				lastReceived: time,
				isRequesting: false,
				error
			}
			break;
	}
	return state;
}


export default options;
