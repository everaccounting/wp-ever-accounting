import {Component, Fragment} from 'react';
import {__} from '@wordpress/i18n';
import {Doughnut} from 'react-chartjs-2';
import {SectionTitle, DashboardCard, Select} from '@eaccounting/components';
import {DATE_RANGE_OPTION} from '@eaccounting/data';
import './style.scss';
import CashFlow from './widgets/cashflow';
import Summery from "./widgets/summery";
import Incomes from "./widgets/incomes";
import Expenses from "./widgets/expenses";
import Accounts from "./widgets/account";
import IncomeCategories from "./widgets/income-categories";
import ExpenseCategories from "./widgets/expense-categories";


export default class Dashboard extends Component {
	constructor(props) {
		super(props);
		this.state = {
			date: null,
		};
	}

	render() {
		const {date} = this.state;
		return (
			<Fragment>
				<SectionTitle title={__('Dashboard')}>
					<Select options={DATE_RANGE_OPTION} onChange={date => this.setState({date})}/>
				</SectionTitle>

				<Summery date={date}/>

				<div className="ea-row">
					<DashboardCard className="ea-col-12" title={__('Cash Flow')}>
						<CashFlow  date={date}/>
					</DashboardCard>
				</div>

				<div className="ea-row">
					<IncomeCategories date={date}/>
					<ExpenseCategories date={date}/>
				</div>

				<div className="ea-row">
					<Incomes/>
					<Expenses/>
					<Accounts/>
				</div>
			</Fragment>
		);
	}
}
