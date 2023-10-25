/**
 * WordPress dependencies
 */
import { registerStore } from '@wordpress/data';
import controls from '@wordpress/data-controls';

/**
 * Internal dependencies
 */
import reducer from './reducer';
import * as selectors from './selectors';
import * as actions from './actions';
import * as resolvers from './resolvers';
import { STORE_NAME } from './constants';

const storeConfig = {
	reducer,
	actions,
	selectors,
	resolvers,
	controls,
};

registerStore( STORE_NAME, storeConfig );

export const SETTINGS_STORE_NAME = STORE_NAME;
