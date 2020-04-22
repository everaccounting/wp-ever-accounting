import {Component} from 'react';
import {__} from '@wordpress/i18n';
import {DashboardCard} from '@eaccounting/components';
import {withSelect} from '@wordpress/data';
import {Spinner} from "@eaccounting/components";
import {Doughnut} from 'react-chartjs-2';

class ExpenseCategories extends Component {

	render() {
		const {isLoading, labels, background_color, data} = this.props;
		return (
			<DashboardCard className="ea-col-6" title={__('Expense By Categories')}>
				{isLoading && <Spinner/>}
				{!isLoading &&  data && <Doughnut data={{
					labels: labels,
					datasets: [
						{
							data: data,
							backgroundColor: background_color,
						},
					],
				}} height={100} options={{
					legend: {
						position: 'right'
					}
				}}/>}

				{!isLoading && !data && <p className="ea-no-result medium">
					{__('There is not enough data to visualize expenses by category graph. Please add expenses.')}
				</p>}

			</DashboardCard>
		)
	}
}

export default withSelect((select, ownProps) => {
	const {date} = ownProps;
	console.log(date);
	const {fetchAPI, isRequestingFetchAPI} = select('ea/collection');
	return {
		...fetchAPI('reports/expense_categories', {date}),
		isLoading: isRequestingFetchAPI('reports/expense_categories', {date}),
	}
})(ExpenseCategories);
