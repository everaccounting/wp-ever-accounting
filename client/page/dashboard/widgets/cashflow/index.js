import {Component, Fragment} from 'react';
import {__} from '@wordpress/i18n';
import {Line} from 'react-chartjs-2';
import {withSelect} from '@wordpress/data';

// const data = {
// 	labels: ['January', 'February', 'March', 'April', 'May', 'June', 'July'],
// 	datasets: [
// 		{
// 			label: 'Income',
// 			fill: false,
// 			lineTension: 0.1,
// 			backgroundColor: 'rgba(75,192,192,0.4)',
// 			borderColor: '#00c0ef',
// 			data: [65, 59, 80, 81, 56, 55, 40],
// 		},
// 		{
// 			label: 'Expense',
// 			fill: false,
// 			lineTension: 0.1,
// 			backgroundColor: 'rgba(75,192,192,0.4)',
// 			borderColor: '#dd4b39',
// 			data: [10, 30, 40, 81, 56, 80, 40],
// 		},
// 		{
// 			label: 'Profit',
// 			fill: false,
// 			lineTension: 0.1,
// 			backgroundColor: 'rgba(75,192,192,0.4)',
// 			borderColor: '#6da252',
// 			data: [40, 50, 40, 81, 70, 80, 40],
// 		},
// 	],
// };

class CashFlow extends Component {
	render() {
		const {data, isLoading} = this.props;
		return (
			<Fragment>
				{isLoading ? <p>Loading</p> : <Line
					data={data}
					height={300}
					options={{
						aspectRatio: 1,
						responsive: true,
						maintainAspectRatio: false,
					}}
				/>}
			</Fragment>
		);
	}
}

export default withSelect((select, ownProps) => {
	const {date} = ownProps;
	const {fetchAPI, isRequestingFetchAPI} = select('ea/collection');
	return {
		data: fetchAPI('reports/cashflow', {date}),
		isLoading: isRequestingFetchAPI('reports/cashflow', {date}),
	}
})(CashFlow);
