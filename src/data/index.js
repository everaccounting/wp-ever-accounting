/**
 * External dependencies
 */
import { registerStore } from '@wordpress/data';
import { controls as dataControls } from '@wordpress/data-controls';

/**
 * Internal dependencies
 */

export const STORE_KEY = 'ea/collection';
import * as selectors from './selectors';
import * as actions from './actions';
import * as resolvers from './resolvers';
import reducer from './reducers';

import { controls } from './controls';

registerStore( STORE_KEY, {
	reducer,
	actions,
	controls: { ...dataControls, ...controls },
	selectors,
	resolvers,
} );

