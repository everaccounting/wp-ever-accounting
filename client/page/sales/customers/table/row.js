import { Component, Fragment } from 'react';
import PropTypes from 'prop-types';
import { RowActions } from '@eaccounting/components';
import { __ } from '@wordpress/i18n';
import { Link } from 'react-router-dom';
import moment from 'moment';
import { FORMAT_SITE_DATE } from '@eaccounting/data';

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
		const { isLoading, item, isSelected, match, history } = this.props;
		const { id, name, email, phone } = item;
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
						<Link to={`${history.location.pathname}/${id}/edit`}>{this.props.getTableProp(name)}</Link>
					</td>

					<td className="column-email">{this.props.getTableProp(email)}</td>
					<td className="column-phone">{this.props.getTableProp(phone)}</td>
					<td className="column-actions">
						<RowActions
							controls={[
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
