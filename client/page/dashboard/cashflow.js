import {Component, Fragment} from 'react';
import {__} from '@wordpress/i18n';
import {DashboardCard, Select} from "@eaccounting/components";
import {Line} from 'react-chartjs-2';

const data = {
	labels: ['January', 'February', 'March', 'April', 'May', 'June', 'July'],
	datasets: [
		{
			label: 'Income',
			fill: false,
			lineTension: 0.1,
			backgroundColor: 'rgba(75,192,192,0.4)',
			borderColor: '#00c0ef',
			// borderCapStyle: 'butt',
			// borderDash: [],
			// borderDashOffset: 0.0,
			// borderJoinStyle: 'miter',
			// pointBorderColor: 'rgba(75,192,192,1)',
			// pointBackgroundColor: '#fff',
			// pointBorderWidth: 1,
			// pointHoverRadius: 5,
			// pointHoverBackgroundColor: 'rgba(75,192,192,1)',
			// pointHoverBorderColor: 'rgba(220,220,220,1)',
			// pointHoverBorderWidth: 2,
			// pointRadius: 1,
			// pointHitRadius: 10,
			data: [65, 59, 80, 81, 56, 55, 40]
		},{
			label: 'Expense',
			fill: false,
			lineTension: 0.1,
			backgroundColor: 'rgba(75,192,192,0.4)',
			borderColor: '#dd4b39',
			// borderCapStyle: 'butt',
			// borderDash: [],
			// borderDashOffset: 0.0,
			// borderJoinStyle: 'miter',
			// pointBorderWidth: 1,
			// pointHoverRadius: 5,
			// pointHoverBackgroundColor: 'rgba(75,192,192,1)',
			// pointHoverBorderColor: 'rgba(220,220,220,1)',
			// pointHoverBorderWidth: 2,
			// pointRadius: 1,
			// pointHitRadius: 10,
			data: [10, 30, 40, 81, 56, 80, 40]
		},{
			label: 'Profit',
			fill: false,
			lineTension: 0.1,
			backgroundColor: 'rgba(75,192,192,0.4)',
			borderColor: '#6da252',
			// borderCapStyle: 'butt',
			// borderDash: [],
			// borderDashOffset: 0.0,
			// borderJoinStyle: 'miter',
			// pointBorderColor: 'rgba(75,192,192,1)',
			// pointBackgroundColor: '#fff',
			// pointBorderWidth: 1,
			// pointHoverRadius: 5,
			// pointHoverBackgroundColor: 'rgba(75,192,192,1)',
			// pointHoverBorderColor: 'rgba(220,220,220,1)',
			// pointHoverBorderWidth: 2,
			// pointRadius: 1,
			// pointHitRadius: 10,
			data: [40, 50, 40, 81, 70, 80, 40]
		}
	]
};


export default class CashFlow extends Component {
	render() {
		return (
			<Line
				data={data}
				height={300}
				options={{
					aspectRatio: 1,
					responsive: true,
					maintainAspectRatio: false,
				}}/>
		)
	}
}
