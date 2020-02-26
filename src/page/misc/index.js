/**
 * External dependencies
 */

import React, {Component, Fragment} from 'react';
import { translate as __ } from 'lib/locale';


/**
 * Internal dependencies
 */
import './style.scss';
import {HashRouter as Router, NavLink, Route, Switch} from "react-router-dom";
import Categories from "./components/categories";
import Currencies from "./components/currencies";
import TaxRates from "./components/taxrates";


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
		name: __('TaxRates'),
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
