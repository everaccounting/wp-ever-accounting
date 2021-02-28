/**
 * WordPress dependencies
 */
import { registerStore } from '@wordpress/data';
import {controls as dataControls} from '@wordpress/data-controls';

/**
 * Internal dependencies
 */
import { STORE_NAME as REDUCER_KEY } from './contants';
import reducer from './reducer';
import * as selectors from './selectors';
import * as actions from './actions';
import * as resolvers from './resolvers';
import * as locksSelectors from './locks/selectors';
import * as locksActions from './locks/actions';
import controls from './controls';

registerStore( REDUCER_KEY, {
	reducer,
	controls:{...controls, ...dataControls},
	actions: { ...actions, ...locksActions},
	selectors: {...selectors, ...locksSelectors},
	resolvers: resolvers,
} );

export const STORE_NAME = REDUCER_KEY;
