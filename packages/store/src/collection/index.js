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
import * as actionGenerators from './action-generators';


/**
 * Registers the store for the 'ea/lists` reducer.
 */
export default registerStore(REDUCER_KEY, {
	reducer,
	actions: {...actions, ...actionGenerators},
	selectors: selectors,
	resolvers: resolvers,
	controls,
});


export const COLLECTION_KEY = REDUCER_KEY;
