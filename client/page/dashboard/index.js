import {Component, Fragment} from 'react';
import {__} from '@wordpress/i18n';
import {SectionTitle, DashboardCard, Select} from "@eaccounting/components";
import {Dashicon} from "@wordpress/components";
import {DATE_RANGE_OPTION} from "@eaccounting/data";
import "./style.scss";
import {Doughnut} from 'react-chartjs-2';
import CashFlow from "./cashflow";

const data = {
	labels: [
		'Red',
		'Green',
		'Yellow'
	],
	datasets: [{
		data: [300, 50, 100],
		backgroundColor: [
			'#FF6384',
			'#36A2EB',
			'#FFCE56'
		],
		hoverBackgroundColor: [
			'#FF6384',
			'#36A2EB',
			'#FFCE56'
		]
	}]
};
export default class Dashboard extends Component {
	constructor(props) {
		super(props);
		this.state = {};
	}

	render() {
		return (
			<Fragment>
				<SectionTitle title={__('Dashboard')}>
					<Select options={DATE_RANGE_OPTION}
							onChange={value => console.log(value)}/>
				</SectionTitle>

				<div className="ea-row">
					<DashboardCard className="ea-col-4 ea-summery-box">
						<div className="ea-summery-box__icon income">
							<Dashicon icon="chart-pie" size="50"/>
						</div>
						<div className="ea-summery-box__content">
							<span className="ea-summery-box__text">Total Incomes</span>
							<span className="ea-summery-box__number">৳14,486,051.97</span>
						</div>

					</DashboardCard>

					<DashboardCard className="ea-col-4 ea-summery-box">
						<div className="ea-summery-box__icon expense">
							<Dashicon icon="cart" size="50"/>
						</div>
						<div className="ea-summery-box__content">
							<span className="ea-summery-box__text">Total Expense</span>
							<span className="ea-summery-box__number">৳14,486,051.97</span>
						</div>

					</DashboardCard>

					<DashboardCard className="ea-col-4 ea-summery-box">
						<div className="ea-summery-box__icon profit">
							<Dashicon icon="heart" size="50"/>
						</div>
						<div className="ea-summery-box__content">
							<span className="ea-summery-box__text">Total Profit</span>
							<span className="ea-summery-box__number">৳14,486,051.97</span>
						</div>

					</DashboardCard>

				</div>

				<div className="ea-row">
					<DashboardCard className="ea-col-12" title={__('Cash Flow')}>
						<CashFlow/>
					</DashboardCard>
				</div>

				<div className="ea-row">
					<DashboardCard className="ea-col-6" title={__('Income By Categories')}>
						<Doughnut data={data} height={100}/>
					</DashboardCard>
					<DashboardCard className="ea-col-6" title={__('Expense By Categories')}>
						<Doughnut data={data}/>
					</DashboardCard>
				</div>


				<div className="ea-row">
					<DashboardCard className="ea-col-4" title={__('Latest Expense')}>
						<table className="ea-table">
							<thead>
							<tr>
								<th>Date</th>
								<th>Category</th>
								<th>Amount</th>
							</tr>
							</thead>
							<tbody>
							<tr>
								<td>2020-04-07</td>
								<td>Payroll – Salary &amp; Wages</td>
								<td>৳5,000.00</td>
							</tr>
							<tr>
								<td>2020-04-07</td>
								<td>House Rent</td>
								<td>৳38,000.00</td>
							</tr>
							<tr>
								<td>2020-04-06</td>
								<td>Payroll – Salary &amp; Wages</td>
								<td>৳324,866.00</td>
							</tr>
							<tr>
								<td>2020-03-14</td>
								<td>Lunch</td>
								<td>৳694.00</td>
							</tr>
							<tr>
								<td>2020-03-13</td>
								<td>Repair &amp; Maintenance</td>
								<td>৳150.00</td>
							</tr>
							</tbody>
							<tbody>

							</tbody>

						</table>
					</DashboardCard>
					<DashboardCard className="ea-col-4" title={__('Latest Income')}>lorem100</DashboardCard>
					<DashboardCard className="ea-col-4" title={__('Account Balance')}>lorem100</DashboardCard>
				</div>


			</Fragment>
		);
	}
}

