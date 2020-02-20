/**
 * External dependencies
 */

import React, {Component, Fragment} from 'react';
import {translate as __} from 'lib/locale';
import {HashRouter as Router, Route, Switch, NavLink} from 'react-router-dom';

/**
 * Internal dependencies
 */
import './style.scss';
import Payments from './components/payments';
import Bills from "./components/bills";

const getTabs = [
	{
		path: '/expenses',
		component: Payments,
		name: __('Payments'),
	},
	{
		path: '/expenses/bills',
		component: Bills,
		name: __('Bills'),
	}
];

export default class Expenses extends Component {
	constructor(props) {
		super(props);
		this.state = {};
		window.addEventListener('popstate', this.onPageChanged);
	}

	componentDidCatch(error, info) {
		this.setState({error: true, stack: error, info});
	}

	render() {
		return (
			<Fragment>
				<h1 className="wp-heading-inline">{__('Expenses')}</h1>
				<Router>
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

			</Fragment>
		)
	}
}
