/**
 * External dependencies
 */
import {registerStore} from '@wordpress/data';
import controls from '../base-controls';
/**
 * Internal dependencies
 */
import reducer from './reducers';
import * as selectors from './selectors';
import * as actions from './actions';
import * as resolvers from './resolvers';
import {REDUCER_KEY} from './constants';

export default registerStore(REDUCER_KEY, {
	reducer,
	actions,
	controls,
	selectors: selectors,
	resolvers: resolvers,
});

export const SCHEMA_KEY = REDUCER_KEY;
