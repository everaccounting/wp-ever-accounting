/* global eAccountingi10n */
import { applyMiddleware, createStore } from 'redux';
import { composeWithDevTools } from 'redux-devtools-extension/developmentOnly';
import thunk from 'redux-thunk';
import reducers from './reducers';
import { createPromise } from 'redux-promise-middleware';
import logger from 'redux-logger';

const composeEnhancers = composeWithDevTools({
	name: 'eaccounting',
});

var middlewares = [
	createPromise({
		promiseTypeSuffixes: ['LOADING', 'SUCCESS', 'FAILED'],
	}),
	thunk,
];

if (process.env.NODE_ENV === 'development') {
	middlewares.push(logger);
}

export default function createReduxStore(initialState = {}) {
	return createStore(reducers, initialState, composeEnhancers(applyMiddleware(...middlewares)));
}
