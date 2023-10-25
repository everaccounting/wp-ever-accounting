export function reducer( state = {}, action ) {
	switch ( action.type ) {
		case 'RECEIVE_SETTINGS':
			return {
				...state,
				...action.settings.reduce( ( memo, setting ) => {
					return {
						...memo,
						[ setting.name ]: {
							...state[ setting.name ],
							...setting,
							error: null,
							lastReceived: action.time ?? Date.now(),
							isRequesting: false,
						},
					};
				}, {} ),
			};
		case 'RECEIVE_SETTINGS_ERROR':
			return {
				...state,
				...action.errors.reduce( ( memo, error ) => {
					return {
						...memo,
						[ error.name ]: {
							...state[ error.name ],
							error: error.error,
							lastReceived: action.time ?? Date.now(),
							isRequesting: false,
						},
					};
				}, {} ),
			};
		case 'SET_REQUESTING_SETTINGS':
			const { names = [] } = action;
			return {
				...state,
				...names.reduce( ( memo, name ) => {
					return {
						...memo,
						[ name ]: {
							...state[ name ],
							isRequesting: true,
						},
					};
				}, {} ),
			};
	}
	return state;
}

export default reducer;
