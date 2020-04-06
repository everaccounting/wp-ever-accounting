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
		const { id, from_account, to_account, amount, transferred_at  } = item;
		const {history} = this.props;
		return (
			<Fragment>
				<tr className={isLoading ? 'disabled' : ''}>
					<th scope="row" className="column-primary column-date">
						<Link to={`${history.location.pathname}/edit/${id}`}>{moment(transferred_at).format(FORMAT_SITE_DATE)}</Link>
					</th>

					<td className="column-amount">{amount}</td>
					<td className="column-from_account">{from_account && from_account.name && from_account.name || '&mdash'}</td>
					<td className="column-to_account">{to_account && to_account.name && to_account.name || '&mdash'}</td>
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
