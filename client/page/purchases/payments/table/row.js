import {Component, Fragment} from 'react';
import PropTypes from 'prop-types';
import {RowActions} from '@eaccounting/components';
import {__} from '@wordpress/i18n';
import {Link} from "react-router-dom";
import moment from "moment";
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
		const {isLoading, item, isSelected, match, history} = this.props;
		const {id, paid_at, amount, account, contact, category} = item;
		return (
			<Fragment>
				<tr className={isLoading ? 'disabled' : ''}>

					<th scope="row" className="check-column">
						<input
							type="checkbox"
							name="item[]"
							value={id}
							disabled={isLoading}
							checked={isSelected}
							onChange={() => this.props.setSelected(item.id)}
						/>
					</th>

					<td scope="row" className="column-primary column-name">
						<Link to={`${history.location.pathname}/${id}/view`}>{moment(paid_at).format(FORMAT_SITE_DATE)}</Link>
					</td>
					<td className="column-money">{this.props.getTableProp(amount)}</td>
					<td className="column-category">{this.props.getTableProp(category, ['name'])}</td>
					<td className="column-account">{this.props.getTableProp(account, ['name'])}</td>
					<td className="column-contact">{this.props.getTableProp(contact, ['name'])}</td>
					<td className="column-actions">
						<RowActions controls={[
								{
									title: __('Edit'),
									onClick: () => history.push(`${match.path}/${id}/edit`),
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
