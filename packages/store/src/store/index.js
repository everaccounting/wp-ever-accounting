/**
 * WordPress dependencies
 */
import { registerStore } from '@wordpress/data';

/**
 * Internal dependencies
 */
import reducer from './reducers';
import * as selectors from './selectors';
import * as actions from './actions';
import * as resolvers from './resolvers';
import { REDUCER_KEY } from './constants';
import controls from '../base-controls';

export default registerStore(REDUCER_KEY, {
	reducer,
	actions,
	controls,
	selectors,
	resolvers,
});

export const STORE_KEY = REDUCER_KEY;
