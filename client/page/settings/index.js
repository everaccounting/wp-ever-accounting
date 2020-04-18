import {Component, Fragment} from 'react';
import {HashRouter as Router, Redirect, Route, Switch} from 'react-router-dom';
import {applyFilters} from "@wordpress/hooks"
import {__} from '@wordpress/i18n';

import Tabs from "components/tabs";
import General from "./general";
import Defaults from "./defaults";
import "./currencies";
import "./categories";

const tabs = applyFilters('EA_SETTINGS_PAGES', [
	{
		path: '/settings/general',
		component: General,
		name: __('General'),
	},
	{
		path: '/settings/defaults',
		component: Defaults,
		name: __('Defaults'),
	},
	// {
	// 	path: '/settings/invoice',
	// 	component: Invoice,
	// 	name: __('Invoice'),
	// },
	// {
	// 	path: '/settings/taxrates',
	// 	component: TaxRates,
	// 	name: __('TaxRates'),
	// }
]);

export default class Banking extends Component {
	constructor(props) {
		super(props);
	}

	render() {
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
						<Redirect from="/settings" to="/settings/general"/>
					</Switch>
				</Router>
			</Fragment>
		);
	}
}
