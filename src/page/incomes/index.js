/**
 * External dependencies
 */

import React, {Component, Fragment} from 'react';
import {translate as __} from 'lib/locale';
import {HashRouter as Router, Route, Switch, NavLink, Link} from 'react-router-dom';

/**
 * Internal dependencies
 */
import './style.scss';
import Revenues from './components/revenues';
// import Invoices from "./components/invoices";

const getTabs = [
	{
		path: '/incomes',
		component: Revenues,
		name: __('Revenues'),
	},
	{
		path: '/incomes/revenues',
		component: Revenues,
		name: __('Revenues'),
	},
	{
		path: '/incomes/invoices',
		component: Revenues,
		name: __('Invoices'),
	},
	// {
	// 	path: '/incomes/invoices',
	// 	component: Invoices,
	// 	name: __('Invoices'),
	// }
];

export default class Incomes extends Component {
	constructor(props) {
		super(props);
		this.state = {};
	}

	render() {
		const {params} = this.props;
		const {section = 'revenues'} = params;
		console.log(this.props);
		return (
			<Fragment>
				<h1 className="wp-heading-inline">{__('Incomes')}</h1>
				<Router>
					<nav className="nav-tab-wrapper eaccounting-nav-tab-wrapper">
						{getTabs.map((tab, index) => {
							// return (tab.name  && <Link key={index} to={tab.path} className={'nav-tab'}
							// 							  exact
							// 				 activeClassName={'nav-tab-active'} isActive={(match, location)=> {
							// 				 	console.log(location);
							// 				 	console.log(match);
							// }}>{tab.name}</Link>);
							return (tab.name  && <Link key={index} to={tab.path} className={'nav-tab'}>{tab.name}</Link>);
						})}
					</nav>

					<Switch>
						{getTabs.map((tab, index) => {
							return(<Route exact key={index}  path={tab.path} component={(props) => <tab.component {...props}/>}/>);
						})}
					</Switch>

				</Router>

			</Fragment>
		)
	}
}
