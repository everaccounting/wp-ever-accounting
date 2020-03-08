/**
 * External dependencies
 */
import React from 'react';
import { Provider } from 'react-redux';
import { HashRouter as Router, Route, Switch, Redirect } from 'react-router-dom';
import { NotificationContainer } from 'react-notifications';

/**
 * Internal dependencies
 */
import createReduxStore from 'store';
import { routes } from './routes';
import getInitialState from 'store/initial';

const store = createReduxStore(getInitialState());

const App = () => (
	<Provider store={store}>
		<Router>
			<Switch>
				{routes.map(page => {
					return (
						<Route key={page.path} path={page.path} exact render={props => <page.container page={page} {...props} />} />
					);
				})}
				<Redirect from="*" to="/" />
			</Switch>
		</Router>
		<NotificationContainer />
	</Provider>
);

export default App;
