/**
 * External dependencies
 */
import { isEmpty } from 'lodash';

/**
 * State for storing settings options.
 *
 * @param  {Object} state  Current state.
 * @param  {Object} action Dispatched action.
 *
 * @return {Object} Updated state.
 */

export function settings( state = {}, action ) {
	const { settings = [], type, error } = action;
	switch ( type ) {
		case 'RECEIVE_SETTINGS':
			state = {
				...state,
				...settings.reduce( ( result, setting ) => {
					return {
						...result,
						[ setting.id ]:
							isEmpty( setting.value ) && setting.default
								? setting.default
								: setting.value,
					};
				}, {} ),
				error,
			};
			break;
	}
	return state;
}

export default settings;
