import { Component, Fragment } from 'react';
import { HashRouter as Router, Redirect, Route, Switch } from 'react-router-dom';
import { applyFilters } from '@wordpress/hooks';
import {__} from '@wordpress/i18n';
import Tabs from 'components/tabs';
import IncomeReport from "./income";
import ExpenseReport from "./expense";
import IncomeExpenseReport from "./income-expense";

import './style.scss';

const tabs = applyFilters('EA_REPORTS_PAGES', [
	{
		path: '/reports/income',
		component: IncomeReport,
		name: __('Income'),
	},
	{
		path: '/reports/expense',
		component: ExpenseReport,
		name: __('Expense'),
	},
	{
		path: '/reports/income_expese',
		component: IncomeExpenseReport,
		name: __('Income vs Expense'),
	}
]);

export default class Reports extends Component {
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
						<Redirect from="/reports" to="/reports/income" />
					</Switch>
				</Router>
			</Fragment>
		);
	}
}
