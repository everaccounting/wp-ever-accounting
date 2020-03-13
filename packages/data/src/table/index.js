import {registerStore} from '@wordpress/data';
import {controls as dataControls} from '@wordpress/data-controls';
export const TABLE_KEY = 'table';
import * as selectors from './selectors';
import * as actions from './actions';
import * as resolvers from './resolvers';
import reducer from './reducers';
import {controls} from './controls';
export const TABLE_STORE_KEY = "ea/store/table";
const initialState = {
	rows: [],
	saving: [],
	total: 0,
	status: "STATUS_IN_PROGRESS",
	table:{
		page: 1,
		per_page: 20,
		selected: [],
	}
};

registerStore(TABLE_STORE_KEY, {
	reducer,
	actions,
	controls: {...dataControls, ...controls},
	selectors,
	initialState,
	resolvers,
});
