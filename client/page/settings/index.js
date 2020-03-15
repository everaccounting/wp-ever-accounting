/**
 * External dependencies
 */
import {Fragment} from "@wordpress/element";
import { __ } from '@wordpress/i18n';
import { HashRouter as Router, Redirect, Route, Switch } from 'react-router-dom';
import General from "./sections/general";
import Company from "./sections/company";
import Defaults from "./sections/defaults";
import Tabs from 'components/tabs';

import { getHistory } from '@eaccounting/navigation';
const getTabs = [
	{
		path: '/settings/general',
		container: General,
		name: __('General'),
	},
	{
		path: '/settings/company',
		container: Company,
		name: __('Company'),
	},
	{
		path: '/settings/defaults',
		container: Defaults,
		name: __('Defaults'),
	},
];


const Settings = props => {
	return (
		<Fragment>
			<Tabs tabs={getTabs} />
			<Router history={ getHistory() }>
				<Switch>
					{getTabs.map((page)=> {
						return (
							<Route key={page.path} path={page.path} exact render={props => <page.container page={page} {...props} />} />
						);
					})}
					<Redirect from="/settings" to="/settings/general" />
				</Switch>
			</Router>
		</Fragment>
	);
};

export default Settings;
