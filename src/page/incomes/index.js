/**
 * External dependencies
 */

import React, {Component, Fragment} from 'react';
import {translate as __} from 'lib/locale';
import {HashRouter as Router, Route, Switch, NavLink, Link, Redirect} from 'react-router-dom';

/**
 * Internal dependencies
 */
import './style.scss';
import Revenues from './components/revenues';
import EditRevenue from "../../component/edit-revenue";
// import Invoices from "./components/invoices";

const getTabs = [
	{
		path: '/incomes/revenues',
		component: Revenues,
		name: __('Revenues'),
	},
	{
		path: '/incomes/revenues/new',
		component: EditRevenue,
	},
	{
		path: '/incomes/revenues/:id',
		component: EditRevenue,
	},
	{
		path: '/incomes/invoices',
		component: Revenues,
		name: __('Invoices'),
	},
	{
		path: '/incomes/invoices/new',
		component: Revenues,
	},
	{
		path: '/incomes/invoices/:id',
		component: Revenues,
	},
];

export default class Incomes extends Component {
	constructor(props) {
		super(props);
		this.state = {};
	}

	render() {
		return (
			<Fragment>
				<h1 className="wp-heading-inline">{__('Incomes')}</h1>
				<Router>
					<nav className="nav-tab-wrapper eaccounting-nav-tab-wrapper">
						{getTabs.map((tab, index) => {
							return (tab.name && <NavLink key={index} to={tab.path} className={'nav-tab'} activeClassName={'nav-tab-active'} isActive={(match, location)=> {
								return location.pathname.includes(tab.path);
							}}>{tab.name}</NavLink>);
						})}
					</nav>

					<Switch>
						{getTabs.map((tab, index) => {
							return(<Route exact key={index}  path={tab.path} component={(props) => <tab.component {...props}/>}/>);
						})}
						<Redirect to="/incomes/revenues"  from="/incomes" exact/>
					</Switch>

				</Router>

			</Fragment>
		)
	}
}
