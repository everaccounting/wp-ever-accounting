/**
 * External dependencies
 */

import {Component, Fragment} from 'react';
// import { translate as __ } from 'lib/locale';
import {NotificationContainer} from 'react-notifications';
import {applyFilters} from '@wordpress/hooks';
import {HashRouter as Router, Route, Switch} from 'react-router-dom';
/**
 * Internal dependencies
 */
import './style.scss';
import Dashboard from "../dashboard";
import Contacts from "../contacts";
import Contact from "../contact";
import Transactions from "../transactions";
import Accounts from "../accounts";
import Revenues from "../revenues";
import Payments from "../payments";

const pages = [
	{
		path: '/',
		exact: true,
		title: 'Dashboard',
		priority: 10,
		component: Dashboard
	},
	{
		path: '/dashboard',
		exact: true,
		title: 'Dashboard',
		priority: 10,
		component: Dashboard
	},
	{
		path: '/contacts',
		exact: true,
		title: 'Contacts',
		priority: 10,
		component: Contacts
	},
	{
		path: '/contacts:id',
		exact: true,
		title: 'Contacts',
		priority: 10,
		component: Contact
	},
	{
		path: '/transactions',
		exact: true,
		title: 'Contacts',
		priority: 10,
		component: Transactions
	},
	{
		path: '/payments',
		exact: true,
		title: 'Contacts',
		priority: 10,
		component: Payments
	},
	{
		path: '/revenues',
		exact: true,
		title: 'Contacts',
		priority: 10,
		component: Revenues
	},
	{
		path: '/accounts',
		exact: true,
		title: 'Contacts',
		priority: 10,
		component: Accounts
	}
];
export default class Home extends Component {
	constructor(props) {
		super(props);
		this.state = {};
		window.addEventListener('popstate', this.onPageChanged);
	}

	componentDidCatch(error, info) {
		this.setState({error: true, stack: error, info});
	}

	componentWillUnmount() {
		window.removeEventListener('popstate', this.onPageChanged);
	}


	render() {
		const menus = applyFilters('eaccounting_main_menus', pages.sort((a, b) => a.priority - b.priority));
		return (
			<Fragment>
				<Router>
					<Switch>
						{menus.map((menu, index) => {
							return (
								<Route key={index} path={menu.path} exact={menu.exact}
									   component={(props) => <menu.component {...props}/>}/>
							)
						})}
					</Switch>
					<NotificationContainer/>
				</Router>
			</Fragment>
		);
	}
}
// function mapDispatchToProps( dispatch ) {
// 	return {}
// }
// function mapStateToProps( state ) {
// 	return {}
// }
//
// export default connect(
// 	mapStateToProps,
// 	mapDispatchToProps,
// )( Home );
