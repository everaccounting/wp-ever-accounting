import {ACTION_TYPES as types} from './action-types';
import {hasInState, updateState} from '../utils';
import {xor, has} from "lodash";
import {setSelected} from "./utils";
import {META_KEY, ITEMS_KEY} from "./constants";

const DEFAULT = {
	total: 0,
	selected: [],
	status: "COMPLETE",
	"": [],
};

const reducers = (state = {}, action) => {
	const {type, endpoint, query = '', response} = action;
	console.log("action", action);
	switch (type) {
		case types.RECEIVE_COLLECTION:
			// if (!hasInState(state, [endpoint])) {
			// 	state = updateState(state, [endpoint], DEFAULT)
			// }
			// console.log(query);
			state = updateState(state, [endpoint, 'total'], response.headers.get('x-wp-total'));
			state = updateState(state, [endpoint, query, 'items'], response.items);
			state = updateState(state, [endpoint, query, 'headers'], response.headers);


			// console.log(action.response.headers.get('x-wp-total'));
			// if (hasInState(state, [endpoint, query])) {
			// 	return state
			// }
			// state = {
			// 	...state,
			// 	[endpoint]: {
			// 		...state[endpoint],
			// 		[query]: response.items,
			// 		total:  response.total ? response.total : has(state, [endpoint, 'total']) ? state[endpoint]['total'] : 0,
			// 		selected: has(state, [endpoint, 'selected'])?state[endpoint]['selected']:[],
			// 		status:"COMPLETE"
			// 	}
			// };
			// // console.group("Reducer");
			// console.log(action);
			// console.log(state);
			// console.groupEnd();
			// state = {...state, [endpoint]: {...state[endpoint], meta: {...DEFAULT_META, ...response.meta}}};

			// state = {...state, [endpoint]: {...state[endpoint], [query]: response.items}};
			break;
		case types.SELECT_COLLECTION_ITEM:
			// state = updateState(state, [endpoint], {
			// 	...state[endpoint],
			// 	meta: {...state[endpoint]['meta'], selected: xor(state[endpoint]['meta']['selected'], [action.id])}
			// });
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
		case types.FAILED:
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
