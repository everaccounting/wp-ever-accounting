/**
 * WordPress dependencies
 */
import { registerStore } from '@wordpress/data';
import {controls as dataControls } from '@wordpress/data-controls';
import {STORE_KEY} from "./constants";
import * as resolvers from './resolvers';
import * as selectors from './selectors';
import * as actions from './actions';
import reducer from './reducers';
import { controls } from './controls';
registerStore(STORE_KEY, {
	reducer,
	actions,
	controls: { ...dataControls, ...controls },
	selectors,
	resolvers,
});
