/**
 * External dependencies
 */
import React, {Component, Fragment} from 'react';
import { translate as __ } from 'lib/locale';
import {HashRouter as Router, NavLink, Route, Switch} from "react-router-dom";

/**
 * Internal dependencies
 */
import './style.scss';
import Accounts from "./components/accounts";
import Transfers from "./components/transfers";
import Reconciliations from "./components/reconciliations";

const getTabs = [
	{
		path: '/banking',
		component: Accounts,
		name: __('Accounts'),
	},
	{
		path: '/banking/transfers',
		component: Transfers,
		name: __('Transfers'),
	},
	{
		path: '/banking/reconciliations',
		component: Reconciliations,
		name: __('Reconciliations'),
	}
];

export default class Banking extends Component {
	constructor( props ) {
		super(props);
	}

	componentDidCatch( error, info ) {
		this.setState( { error: true, stack: error, info } );
	}

	render() {
		return (
			<Router>
				<h1 className="wp-heading-inline">{__('Banking')}</h1>
				<nav className="nav-tab-wrapper eaccounting-nav-tab-wrapper">
					{getTabs.map((tab, index) => {
						return (<NavLink key={index} exact to={tab.path} className={'nav-tab'}
										 activeClassName={'nav-tab-active'}>{tab.name}</NavLink>);
					})}
				</nav>

				<Switch>
					{getTabs.map((tab, index) => {
						return(<Route exact key={index}  path={tab.path} component={(props) => <tab.component {...props}/>}/>);
					})}
				</Switch>

			</Router>
		);
	}
}
