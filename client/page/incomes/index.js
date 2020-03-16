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
import Revenues from '../revenues';
import Invoices from '../invoices';

const tabs = [
	{
		path: '/incomes/revenues',
		name: __('Revenues'),
		component:Revenues,
	},
	{
		path: '/incomes/invoices',
		name: __('Invoices'),
		component:Invoices,
	},
];

const Incomes = props => {
	return (
		<Fragment>
			<Tabs tabs={tabs}/>
			<Router>
				<Switch>
					{tabs.map(tab => {
						return (
							<Route key={tab.path} path={tab.path} exact render={props => <tab.component  {...props}/>}/>
						);
					})}
					<Redirect from="/incomes" to="/incomes/revenues"/>
				</Switch>
			</Router>
		</Fragment>
	);
};

export default Incomes;
