/**
 * External dependencies
 */
import React, {Component, Fragment} from 'react';
import { translate as __ } from 'lib/locale';
import {HashRouter as Router, Redirect, Route, Switch} from "react-router-dom";

/**
 * Internal dependencies
 */
import Accounts from "../accounts";
import Transfers from "../transfers";
import Reconciliations from "../reconciliations";
import Tabs from "component/tabs";

const getTabs = [
	{
		path: '/banking/accounts',
		name: __('Accounts'),
	},
	{
		path: '/banking/transfers',
		name: __('Transfers'),
	},
	{
		path: '/banking/reconciliations',
		name: __('Reconciliations'),
	}
];

const Banking = (props) => {
	return (
		<Fragment>
			<h1 className="wp-heading-inline">{__('Banking')}</h1>
			<Tabs tabs={getTabs}/>
			<Router>
				<Switch>
					<Route exact path="/banking/accounts" component={Accounts}/>
					<Route exact path="/banking/transfers" component={Transfers}/>
					<Route exact path="/banking/reconciliations" component={Reconciliations}/>
					<Redirect from="/banking" to="/banking/accounts"/>
				</Switch>
			</Router>

		</Fragment>
	)
};

export default Banking
