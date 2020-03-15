/**
 * External dependencies
 */
import {registerStore} from '@wordpress/data';

/**
 * Internal dependencies
 */
export const QUERY_STATE_STORE_KEY = 'ea/store/query-state';
import * as selectors from './selectors';
import * as actions from './actions';
import reducer from './reducers';

const initialState = {
	page: 1,
	per_page: 20,
};

registerStore(QUERY_STATE_STORE_KEY, {
	reducer,
	actions,
	selectors,
	initialState
});
