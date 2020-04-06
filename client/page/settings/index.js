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
import General from './sections/general';
import Defaults from './sections/defaults';
import Invoice from './sections/invoice';

const tabs = [
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
	{
		path: '/settings/invoice',
		component: Invoice,
		name: __('Invoice'),
	},
];

const Settings = props => {

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
};

export default Settings;
