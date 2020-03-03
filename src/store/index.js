/* global eAccountingi10n */
import { createHashHistory } from 'history'
import { applyMiddleware, compose, createStore } from 'redux'
import { routerMiddleware } from 'connected-react-router'
import createRootReducer from './reducers';
import thunk from 'redux-thunk';
import logger from 'redux-logger'
import {createPromise} from 'redux-promise-middleware';
const siteBaseUrl = window.location.href.replace(['http://','https://'],'').replace(eAccountingi10n.baseUrl.replace(['http://','https://'],''), '');
console.log(siteBaseUrl);
export const history = createHashHistory();

var middlewares = [
	createPromise({
		promiseTypeSuffixes: ['LOADING', 'SUCCESS', 'FAILED']
	}),
	thunk,
];

if ( process.env.NODE_ENV === 'development' ) {
	middlewares.push(logger);
}

export default function configureStore(preloadedState) {
	return createStore(
		createRootReducer(history),
		preloadedState,
		compose(
			applyMiddleware(
				routerMiddleware(history),
				...middlewares,
			),
		),
	);
}
