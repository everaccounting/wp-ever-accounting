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
import Accounts from "./components/accounts";
import Transfers from "./components/transfers";
import Reconciliations from "./components/reconciliations";

export default class Banking extends Component {
	constructor(props) {
		super(props);
	}

	componentDidCatch(error, info) {
		this.setState({error: true, stack: error, info});
	}

	render() {
		return (
			<Fragment>
				<Router>

					<nav className="nav-tab-wrapper eaccounting-nav-tab-wrapper">
						<NavLink exact to='/banking' className={'nav-tab'}
								 activeClassName={'nav-tab-active'}>{__('Accounts')}</NavLink>
						<NavLink exact to='/banking/transfers' className={'nav-tab'}
								 activeClassName={'nav-tab-active'}>{__('Transfers')}</NavLink>
						<NavLink exact to='/banking/reconciliations' className={'nav-tab'}
								 activeClassName={'nav-tab-active'}>{__('Reconciliations')}</NavLink>
					</nav>

					<Switch>
						<Route exact path={'/banking'} component={Accounts}/>
						<Route exact path={'/banking/transfers'} component={Transfers}/>
						<Route exact path={'/banking/reconciliations'} component={Reconciliations}/>
					</Switch>
				</Router>

			</Fragment>
		)
	}
}
