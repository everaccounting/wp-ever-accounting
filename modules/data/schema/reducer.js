/**
 * Reducer for processing actions related to the routes store.
 *
 * @param {Object} state  Current state in store.
 * @param {Object} action Action being processed.
 */
export const schema = ( state = {}, action ) => {
	const { type, schema } = action;
	switch ( type ) {
		case 'RECEIVE_SCHEMA':
			return { ...state, ...schema };
		default:
			return state;
	}
};

export default schema;
