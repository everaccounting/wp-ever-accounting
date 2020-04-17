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
		const {isLoading, item, isSelected, match, history, getTableProp} = this.props;
		const {id, from_account, to_account, amount, transferred_at} = item;
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

					<td scope="row" className="column-primary column-date">
						<Link to={`${match.path}/${id}/edit`}>{moment(transferred_at).format(FORMAT_SITE_DATE)}</Link>
					</td>

					<td className="column-amount">{getTableProp(amount)}</td>
					<td className="column-from_account">{getTableProp(from_account, ['name'])}</td>
					<td className="column-to_account">{getTableProp(to_account, ['name'])}</td>
					<td className="column-money">{getTableProp(balance)}</td>
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
