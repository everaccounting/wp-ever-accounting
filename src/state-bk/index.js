/**
 * External dependencies
 */

import {applyMiddleware, createStore,} from 'redux';
import {composeWithDevTools} from 'redux-devtools-extension/developmentOnly';
import thunk from 'redux-thunk';
import reducers from './reducers';
import {urlMiddleware} from 'state/middleware';

/**
 * Internal dependencies
 */

const composeEnhancers = composeWithDevTools( {
	name: 'eaccounting',
} );

const middlewares = [
	thunk,
	urlMiddleware,
];

export default function createReduxStore( initialState = {} ) {
	return createStore(
		reducers,
		initialState,
		composeEnhancers(applyMiddleware(...middlewares))
	);
}
