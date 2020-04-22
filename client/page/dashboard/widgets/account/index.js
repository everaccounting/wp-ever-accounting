import {Component} from 'react';
import {__} from '@wordpress/i18n';
import {DashboardCard} from '@eaccounting/components';
import {withSelect} from '@wordpress/data';
import {Spinner} from "@eaccounting/components";
import {get} from "lodash";

class Accounts extends Component {

	render() {
		const  {isLoading,items } = this.props;
		return(
			<DashboardCard className="ea-col-4" title={__('Account Balance')}>
				{isLoading && <Spinner/>}
				{items && <table className="ea-table">
					<thead>
					<tr>
						<th>{__('Account')}</th>
						<th>{__('Balance')}</th>
					</tr>
					</thead>
					<tbody>
					{items.map((item)=> {
						return(
							<tr key={item.id}>
								<td>{item.name}</td>
								<td>{get(item, 'balance', '-')}</td>
							</tr>
						)
					})}
					</tbody>
				</table>}
				{!isLoading && !items && <p>{__('No accounts found.')}</p>}
			</DashboardCard>
		)
	}
}
export default withSelect((select) => {
	const {getCollection, isRequestingGetCollection} = select('ea/collection');
	const query = {
		per_page:5,
	};
	const {items } = getCollection('accounts', query);
	return {
		items: items,
		isLoading: isRequestingGetCollection('accounts', query),
	}
})(Accounts);
