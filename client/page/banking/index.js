/**
 * External dependencies
 */
import {__} from '@wordpress/i18n';
import {HashRouter as Router, Redirect, Route, Switch} from 'react-router-dom';
import {Fragment} from "@wordpress/element";
import Tabs from "components/tabs";
/**
 * Internal dependencies
 */

import Accounts from '../accounts';

const tabs = [
	{
		path: '/banking/accounts',
		component: Accounts,
		name: __('Accounts'),
	},
	{
		path: '/banking/transfers',
		component: Accounts,
		name: __('Transfers'),
	},
	{
		path: '/banking/reconciliations',
		component: Accounts,
		name: __('Reconciliations'),
	},
];

const Banking = props => {
	return (
		<Fragment>
			<Tabs tabs={tabs}/>
			<Router>
				<Switch>
					{tabs.map(tab => {
						return (
							<Route key={tab.path} path={tab.path} exact
								   render={props => <tab.component  {...props}/>}/>
						);
					})}
					<Redirect from="/banking" to="/banking/accounts"/>
				</Switch>
			</Router>
		</Fragment>
	);
};

export default Banking;
