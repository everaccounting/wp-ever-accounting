/**
 * External dependencies
 */

import { Component, Fragment } from 'react';
// import { translate as __ } from 'lib/locale';
import { NotificationContainer } from 'react-notifications';
import { HashRouter as Router, Route, Switch } from 'react-router-dom';

/**
 * Internal dependencies
 */
import './style.scss';
// import Dashboard from "../dashboard";
// import Contacts from "../contacts";
import Transactions from '../transactions';
import Banking from '../banking';
import Misc from '../misc';
// import Incomes from "../incomes";
// import Expenses from "../expenses";

export default class Home extends Component {
	constructor(props) {
		super(props);
		this.state = {};
		// window.addEventListener('popstate', this.onPageChanged);
	}

	// componentDidCatch(error, info) {
	// 	this.setState({error: true, stack: error, info});
	// }
	//
	// componentWillUnmount() {
	// 	window.removeEventListener('popstate', this.onPageChanged);
	// }

	render() {
		return (
			<Fragment>
				<Router history={getHistory()}>
					<Switch>
						{/*<Route exact path='/' component={Dashboard}/>*/}
						{/*<Route path='/dashboard' component={Dashboard}/>*/}
						<Route path="/transactions" component={Transactions} />
						{/*<Route path='/contacts' component={Contacts}/>*/}
						{/*<Route path='/incomes:tab' component={Incomes}/>*/}
						{/*<Route path='/incomes' component={Incomes}/>*/}
						{/*<Route path='/expenses:tab' component={Expenses}/>*/}
						{/*<Route path='/expenses' component={Expenses}/>*/}
						{/*<Route path='/banking:tab' component={Banking}/>*/}
						<Route path="/banking/:tab" component={Banking} />
						<Route path="/banking" component={Banking} />
						<Route path="/misc" component={Misc} />
						{/*<Route  path='/misc:tab' component={Misc}/>*/}
					</Switch>
					<NotificationContainer />
				</Router>
			</Fragment>
		);
	}
}
