/**
 * External dependencies
 */

import React, { Component, Fragment } from 'react';
import { translate as __ } from 'lib/locale';
import { HashRouter as Router, Route, Switch, Redirect } from 'react-router-dom';
import Tabs from 'component/tabs';
import Payments from '../payments';
import Bills from '../bills';

/**
 * Internal dependencies
 */

const getTabs = [
	{
		path: '/expenses/payments',
		name: __('Payments'),
	},
	{
		path: '/expenses/bills',
		name: __('Bills'),
	},
];

const Expenses = props => {
	return (
		<Fragment>
			<Tabs tabs={getTabs} />
			<Router>
				<Switch>
					<Route exact path="/expenses/payments/new" component={Payments} />
					<Route exact path="/expenses/payments/:id" component={Payments} />
					<Route exact path="/expenses/payments" component={Payments} />
					<Route exact path="/expenses/bills" component={Bills} />
					<Redirect from="/expenses" to="/expenses/payments" />
				</Switch>
			</Router>
		</Fragment>
	);
};

export default Expenses;
