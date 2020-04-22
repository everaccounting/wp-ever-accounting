import {Component, Fragment} from 'react';
import {__} from '@wordpress/i18n';
import {withSelect} from '@wordpress/data';
import {addFilter} from '@wordpress/hooks';

class ExpenseReport extends Component {
	render() {
		console.log(this.props);
		return (
			<div>
				Hello
			</div>
		)
	}
}

export default withSelect((select, ownProps) => {
	const {date} = ownProps;
	const {fetchAPI, isRequestingFetchAPI} = select('ea/collection');
	return {
		summery: fetchAPI('reports/expense_report', {date}),
		isLoading: isRequestingFetchAPI('reports/expense_report', {date}),
	}
})(ExpenseReport);
