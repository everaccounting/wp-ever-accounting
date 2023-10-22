/**
 * WordPress dependencies
 */
import { registerStore } from '@wordpress/data';

/**
 * Internal dependencies
 */
import reducer from './reducer';
import * as selectors from './selectors';
import * as actions from './actions';
import * as resolvers from './resolvers';
import defaultControls from '../controls';
import { STORE_NAME } from './constants';

const storeConfig = {
	reducer,
	actions,
	selectors,
	resolvers,
	controls: defaultControls,
};

registerStore( STORE_NAME, storeConfig );

export const ENTITIES_STORE_NAME = STORE_NAME;
