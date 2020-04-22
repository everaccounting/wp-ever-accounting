import {Component} from 'react';
import {__} from '@wordpress/i18n';
import {DashboardCard} from '@eaccounting/components';
import {withSelect} from '@wordpress/data';
import {Spinner} from "@eaccounting/components";
import {get} from "lodash";
import { FORMAT_SITE_DATE } from '@eaccounting/data';
import moment from 'moment';
import {isEmpty} from "lodash";

class Expenses extends Component {

	render() {
		const  {isLoading,items } = this.props;
		return(
			<DashboardCard className="ea-col-4" title={__('Latest Expense')}>
				{isLoading && <Spinner/>}
				{!isLoading && !isEmpty(items) && <table className="ea-table">
					<thead>
					<tr>
						<th>{__('Date')}</th>
						<th>{__('Category')}</th>
						<th>{__('Amount')}</th>
					</tr>
					</thead>
					<tbody>
					{items.map((item)=> {
						return(
							<tr key={item.id}>
								<td>{moment(item.paid_at).format(FORMAT_SITE_DATE)}</td>
								<td>{get(item, 'category.name', '-')}</td>
								<td>{item.amount}</td>
							</tr>
						)
					})}
					</tbody>
				</table>}

				{!isLoading && isEmpty(items) && <p className="ea-no-result medium">
					{__('There are no recent expenses')}
				</p>}
			</DashboardCard>
		)
	}
}
export default withSelect((select) => {
	const {getCollection, isRequestingGetCollection} = select('ea/collection');
	const query = {
		per_page:5,
		type:'expense',
		orderby:'created_at'
	};
	const {items } = getCollection('transactions', query);
	return {
		items: items,
		isLoading: isRequestingGetCollection('transactions', query),
	}
})(Expenses);
