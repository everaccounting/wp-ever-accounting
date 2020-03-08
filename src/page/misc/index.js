/**
 * External dependencies
 */

import React, { Component, Fragment } from 'react';
import { translate as __ } from 'lib/locale';
import { HashRouter as Router, Redirect, Route, Switch } from 'react-router-dom';

/**
 * Internal dependencies
 */
import Categories from '../categories';
import Currencies from '../currencies';
import TaxRates from '../taxrates';
import Tabs from 'component/tabs';

const getTabs = [
	{
		path: '/misc/categories',
		component: Categories,
		name: __('Categories'),
	},
	{
		path: '/misc/currencies',
		component: Currencies,
		name: __('Currencies'),
	},
	{
		path: '/misc/taxrates',
		component: TaxRates,
		name: __('Tax Rates'),
	},
];

const Misc = props => {
	return (
		<Fragment>
			<Tabs tabs={getTabs} />
			<Router>
				<Switch>
					<Route exact path="/misc/categories" component={Categories} />
					<Route exact path="/misc/currencies" component={Currencies} />
					<Route exact path="/misc/taxrates" component={TaxRates} />
					<Redirect from="/misc" to="/mic/categories" />
				</Switch>
			</Router>
		</Fragment>
	);
};

export default Misc;
