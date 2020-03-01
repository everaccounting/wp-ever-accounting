/**
 * External dependencies
 */

import React, {Component, Fragment} from 'react';
import { translate as __ } from 'lib/locale';
import {HashRouter as Router, NavLink, Route, Switch} from "react-router-dom";

/**
 * Internal dependencies
 */
import Categories from "./components/categories";
import Currencies from "./components/currencies";
import TaxRates from "./components/taxrates";
import './style.scss';


const getTabs = [
	{
		path: '/misc',
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
	}
];
export default class Misc extends Component {
	constructor( props ) {
		super(props);
	}

	componentDidCatch( error, info ) {
		this.setState( { error: true, stack: error, info } );
	}

	render() {
		console.log(this.props);
		return (
			<Router>
				<h1 className="wp-heading-inline">{__('Miscellaneous')}</h1>
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
