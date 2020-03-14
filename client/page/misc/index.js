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
import Categories from '../categories';
// import Currencies from '../currencies';
// import TaxRates from '../taxrates';

const tabs = [
	{
		path: '/misc/categories',
		component: Categories,
		name: __('Categories'),
	},
	// {
	// 	path: '/misc/currencies',
	// 	// component: Currencies,
	// 	name: __('Currencies'),
	// },
	// {
	// 	path: '/misc/taxrates',
	// 	// component: TaxRates,
	// 	name: __('Tax Rates'),
	// },
];

const Misc = props => {
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
					<Redirect from="/misc" to="/misc/categories"/>
				</Switch>
			</Router>
		</Fragment>
	);
};

export default Misc;
