import {Component, Fragment} from 'react';
import {HashRouter as Router, Redirect, Route, Switch} from 'react-router-dom';
import {applyFilters} from "@wordpress/hooks"
import Tabs from "components/tabs";
import "./accounts";
import "./transfers";

const tabs = applyFilters('EA_BANKING_PAGES', []);

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
						<Redirect from="/banking" to="/banking/accounts"/>
					</Switch>
				</Router>
			</Fragment>
		);
	}
}
