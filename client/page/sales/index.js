import {Component, Fragment} from 'react';
import {HashRouter as Router, Redirect, Route, Switch} from 'react-router-dom';
import {applyFilters} from "@wordpress/hooks"
import Tabs from "components/tabs";
import "./revenues";
import "./customers";

const tabs = applyFilters('EA_SALES_PAGES', []);

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
						<Redirect from="/sales" to="/sales/revenues"/>
					</Switch>
				</Router>
			</Fragment>
		);
	}
}
