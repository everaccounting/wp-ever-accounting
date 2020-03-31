/**
 * External dependencies
 */
import {registerStore} from '@wordpress/data';
import {REDUCER_KEY} from './constants';

/**
 * Internal dependencies
 */
import reducer from './reducers';
import * as selectors from './selectors';
import * as actions from './actions';
import * as resolvers from './resolvers';
import controls from '../base-controls';


/**
 * Registers the store for the 'ea/collection` reducer.
 */
export default registerStore(REDUCER_KEY, {
	reducer,
	actions,
	selectors,
	resolvers,
	controls,
});


export const COLLECTION_KEY = REDUCER_KEY;
