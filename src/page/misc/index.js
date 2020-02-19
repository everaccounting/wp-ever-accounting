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


export default class Misc extends Component {
	constructor( props ) {
		super(props);
		this.state = {};
	}

	componentDidCatch( error, info ) {
		this.setState( { error: true, stack: error, info } );
	}

	render() {
		return (
			<Router>

				<nav className="nav-tab-wrapper eaccounting-nav-tab-wrapper">
					<NavLink exact to='/misc' className={'nav-tab'}
							 activeClassName={'nav-tab-active'}>{__('Categories')}</NavLink>
					<NavLink exact to='/misc/currencies' className={'nav-tab'}
							 activeClassName={'nav-tab-active'}>{__('Currencies')}</NavLink>
					<NavLink exact to='/misc/tax-rates' className={'nav-tab'}
							 activeClassName={'nav-tab-active'}>{__('Tax Rates')}</NavLink>
				</nav>

				<Switch>
					{/*<Route exact path={'/banking'} component={Accounts}/>*/}
					{/*<Route exact path={'/banking/transfers'} component={Transfers}/>*/}
					{/*<Route exact path={'/banking/reconciliations'} component={Reconciliations}/>*/}
				</Switch>
			</Router>
		);
	}
}
