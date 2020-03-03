/**
 * External dependencies
 */
import React from 'react';
import {Provider} from 'react-redux';
import {ConnectedRouter} from 'connected-react-router'
import configureStore, {history} from './store';
import Revenues from "./page/revenues";
import getInitialState from "store/initial";
import routes from "./routes";

/**
 * Internal dependencies
 */
const store = configureStore(getInitialState());
const App = () => (
	<Provider store={store}>
		<ConnectedRouter history={history}>
				{routes}
		</ConnectedRouter>
	</Provider>
);

export default App;
