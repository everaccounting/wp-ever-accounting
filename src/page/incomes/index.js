/**
 * External dependencies
 */

import React, {Component, Fragment} from 'react';
import {translate as __} from 'lib/locale';
import {HashRouter as Router, Route, Switch, Redirect} from "react-router-dom"
import Tabs from "component/tabs";
import Revenues from "../revenues";
import Invoices from "../invoices";
import EditRevenue from "../../component/edit-revenue";

/**
 * Internal dependencies
 */

const getTabs = [
	{
		path: '/incomes/revenues',
		name: __('Revenues'),
	},
	{
		path: '/incomes/invoices',
		name: __('Invoices'),
	}
];

const Incomes = (props) => {
	return (
		<Fragment>
			<h1 className="wp-heading-inline">{__('Incomes')}</h1>
			<Tabs tabs={getTabs}/>
			<Router>
				<Switch>
					<Route exact path="/incomes/revenues/new" component={EditRevenue}/>
					<Route exact path="/incomes/revenues/:id" component={EditRevenue}/>
					<Route exact path="/incomes/revenues" component={Revenues}/>
					<Route exact path="/incomes/invoices" component={Invoices}/>
					<Redirect from="/incomes" to="/incomes/revenues"/>
				</Switch>
			</Router>

		</Fragment>
	)
};

export default Incomes
// export default connect((state)=> ({...state.match}))(Incomes)
