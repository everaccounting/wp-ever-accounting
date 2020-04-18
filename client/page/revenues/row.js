import {Component, Fragment} from 'react';
import PropTypes from 'prop-types';
import {RowActions} from '@eaccounting/components';
import {__} from '@wordpress/i18n';
import moment from "moment";
import {Link} from "react-router-dom";
import {FORMAT_SITE_DATE} from "@eaccounting/data";

export default class Row extends Component {
	static propTypes = {
		item: PropTypes.object.isRequired,
		isLoading: PropTypes.bool.isRequired,
		isSelected: PropTypes.bool,
	};

	constructor(props) {
		super(props);
	}


	render() {
		const {isLoading, item} = this.props;
		const {id, paid_at, amount, account, contact, category} = item;
		const {history} = this.props;
		return (
			<Fragment>
				<tr className={isLoading ? 'disabled' : ''}>
					<th scope="row" className="column-primary column-date">
						<Link to={`${history.location.pathname}/view/${id}`}>{moment(paid_at).format(FORMAT_SITE_DATE)}</Link>
					</th>

					<td className="column-amount">{amount}</td>
					<td className="column-category">{category && category.name && category.name || '&mdash'}</td>
					<td className="column-account">{account && account.name && account.name || '&mdash'}</td>
					<td className="column-customer">{contact && contact.first_name && contact.first_name && `${contact.first_name} ${contact.last_name}` || '&mdash'}</td>

					<td className="column-actions">
						<RowActions
							controls={[
								{
									title: __('Edit'),
									onClick: () => history.push(`${history.location.pathname}/edit/${id}`),
									disabled: isLoading,
								},
								{
									title: __('Delete'),
									onClick: () => this.props.handleDelete(id),
									disabled: isLoading,
								},
							]}
						/>
					</td>
				</tr>
			</Fragment>
		);
	}
}

