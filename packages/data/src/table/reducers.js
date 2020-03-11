import {ACTION_TYPES as types} from './action-types';
import { hasInState, updateState } from '../utils';

const DEFAULT_STATE = {
	items: [],
	total: 0,
	selected: [],
	query: {
		orderby: 'id',
		order: 'desc',
		per_page: 20,
		page:1,
	}
};

const reducers = (state = {}, action) => {
	const { type, endpoint, query, response } = action;

	switch (type) {
		case types.RECEIVE_COLLECTION:
			state = updateState(state, [endpoint], {...DEFAULT_STATE, ...response, query:{...DEFAULT_STATE.query,...query}});
			state;
			break;
		case types.SELECT_COLLECTION_ITEM:
			state;
			break;
		case types.SELECT_COLLECTION_ITEMS:
			state;
			break;
		case types.DELETE_COLLECTION_ITEM:
			state;
			break;
		case types.UPDATE_COLLECTION_ITEM:
			state;
			break;
		case types.ERROR:
			state;
			break;
		case types.RECEIVE_LAST_MODIFIED:
			state;
			break;
		case types.INVALIDATE_RESOLUTION_FOR_STORE:
			state;
			break;
	}

	return state;
};

export default reducers;
