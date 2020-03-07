/**
 * External dependencies
 */
import React from 'react';
import { HashRouter as Router, Route, Switch, Redirect } from 'react-router-dom';
import { NotificationContainer } from 'react-notifications';
import store from 'contacts/store';
/**
 * Internal dependencies
 */
import { routes } from './routes';
import Contacts from "./contacts";
const App = () => (
	<div>
		<Contacts/>
	</div>
);

export default App;
