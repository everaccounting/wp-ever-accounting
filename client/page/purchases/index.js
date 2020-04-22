import { Component, Fragment } from 'react';
import { HashRouter as Router, Redirect, Route, Switch } from 'react-router-dom';
import { applyFilters } from '@wordpress/hooks';
import Tabs from 'components/tabs';
import './payments';
import './vendors';

const tabs = applyFilters('EA_PURCHASES_PAGES', []);

export default class Banking extends Component {
	constructor(props) {
		super(props);
	}

	render() {
		return (
			<Fragment>
				<Tabs tabs={tabs} />
				<Router>
					<Switch>
						{tabs.map(tab => {
							return <Route key={tab.path} path={tab.path} exact render={props => <tab.component {...props} />} />;
						})}
						<Redirect from="/purchases" to="/purchases/payments" />
					</Switch>
				</Router>
			</Fragment>
		);
	}
}
