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
import Payments from '../payments';
import Bills from '../bills';

const tabs = [
	{
		path: '/expenses/payments',
		name: __('Payments'),
		component:Payments,
	},
	{
		path: '/incomes/invoices',
		name: __('Bills'),
		component:Bills,
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
					<Redirect from="/expenses" to="/expenses/payments"/>
				</Switch>
			</Router>
		</Fragment>
	);
};

export default Incomes;
