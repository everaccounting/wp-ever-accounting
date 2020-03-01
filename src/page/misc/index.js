/**
 * External dependencies
 */

import React, {Component, Fragment} from 'react';
import { translate as __ } from 'lib/locale';
import {Router, Route, Switch, NavLink} from 'react-router-dom';
import {getHistory, getNewPath, getPath, getQuery} from '@eaccounting/navigation';
/**
 * Internal dependencies
 */
import Categories from "./components/categories";
import Currencies from "./components/currencies";
import TaxRates from "./components/taxrates";
import './style.scss';


const getTabs = [
	{
		path: '',
		component: Categories,
		name: __('Categories'),
	},
	{
		path: 'currencies',
		component: Currencies,
		name: __('Currencies'),
	},
	{
		path: 'taxrates',
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
		console.log(this.props.path);
		return (
			<Router  history={getHistory()}>
				<h1 className="wp-heading-inline">{__('Miscellaneous')}</h1>
				<nav className="nav-tab-wrapper eaccounting-nav-tab-wrapper">
					{getTabs.map((tab, index) => {
						return (<NavLink key={index} exact to={getNewPath(getQuery(), getPath()+'/'+tab.path)} className={'nav-tab'} activeClassName={'nav-tab-active'}>{tab.name}</NavLink>);
					})}
				</nav>

				<Switch>
					{getTabs.map((tab, index) => {
						return(<Route exact key={index}  path={`${this.props.path}/`+tab.path} render={(props) => <tab.component {...props}/>}/>);
					})}
				</Switch>

			</Router>
		);
	}
}
