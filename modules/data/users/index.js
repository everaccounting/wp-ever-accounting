/**
 * External dependencies
 */
/**
 * WordPress dependencies
 */
import { registerStore } from '@wordpress/data';
/**
 * Internal dependencies
 */
import controls from '../base-controls';
/**
 * Internal dependencies
 */
import reducer from './reducer';
import * as selectors from './selectors';
import * as actions from './actions';
import * as resolvers from './resolvers';
import { STORE_NAME } from './constants';

export default registerStore( STORE_NAME, {
	reducer,
	actions,
	controls,
	selectors,
	resolvers,
} );

export const USERS_STORE_NAME = STORE_NAME;
