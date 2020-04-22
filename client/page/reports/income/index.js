import {Component, Fragment} from 'react';
import {__} from '@wordpress/i18n';
import {compose} from '@wordpress/compose';
import {withDispatch, withSelect} from '@wordpress/data';
import {
	CompactCard,
	Card,
	Spinner
} from "@eaccounting/components";
import {get} from 'lodash';
import {Line} from 'react-chartjs-2';
import Filter from './filter';

class IncomeReport extends Component {
	render() {
		const {isLoading, report} = this.props;
		const {dates, categories, incomes, totals, incomes_graph} = report;
		console.log(this.props);
		return (
			<Fragment>
				<Filter onFilter={data => {this.props.setContextQuery('reports/income_report', data)}}/>
				<Card>
					{isLoading && <Spinner/>}

					{!isLoading && <Fragment>
						<div className="ea-report-graph" style={{
							position: "relative",
							width: '100%',
							height: '300px',
						}}>
							<Line
								data={{
									labels: Object.values(dates),
									datasets: [
										{
											label: 'Income',
											fill: false,
											lineTension: 0.1,
											backgroundColor: "#00c0ef",
											borderColor: '#00c0ef',
											data: Object.values(incomes_graph),
										},
									]
								}}
								height={300}
								options={{maintainAspectRatio: false, responsive: true}}
							/>
						</div>


						<div className="ea-table-report">
							<table className="ea-table">
								<thead>
								<tr>
									<th>{__('Categories')}</th>
									{Object.values(dates).map(month => {
										return (
											<th className="align-right" key={month}>
												{month}
											</th>
										)
									})}
								</tr>
								</thead>
								<tbody>
								{Object.keys(incomes).map(categoryId => {
									return (
										<tr key={categoryId}>
											<td>{categories[categoryId]}</td>
											{Object.values(incomes[categoryId]).map((income, i) => {
												return (
													<td className="align-right" key={i}>
														{get(income, 'amount', '-')}
													</td>
												)
											})}
										</tr>
									)
								})}
								</tbody>
								<tfoot>
								<tr>
									<th>{__('Total')}</th>
									{Object.values(totals).map((total, index) => {
										return (
											<th className="align-right" key={index}>
												{get(total, 'amount', '-')}
											</th>
										)
									})}
								</tr>
								</tfoot>
							</table>
						</div>
					</Fragment>}
				</Card>
			</Fragment>
		)
	}
}

export default compose(
	withSelect((select, ownProps) => {
		const {fetchAPI, isRequestingFetchAPI} = select('ea/collection');
		const {getQuery} = select('ea/query');
		const query = getQuery('reports/income_report');
		return {
			report: fetchAPI('reports/income_report', query),
			isLoading: isRequestingFetchAPI('reports/income_report', query),
		}
	}),
	withDispatch(dispatch => {
		const {setQuery, setContextQuery} = dispatch('ea/query');
		return {
			setQuery,
			setContextQuery,
		}
	})
)(IncomeReport);
