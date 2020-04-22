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
		const { isLoading, item, isSelected, match, history, getTableProp } = this.props;
		const { name, sale_price, purchase_price, quantity, image, category } = item;
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

					<td scope="row" className="column-photo">
						<Link to={`${match.path}/${id}/edit`}>{image}</Link>
					</td>

					<td scope="row" className="column-primary column-photo">
						<Link to={`${match.path}/${id}/edit`}>{getTableProp(name)}</Link>
					</td>

					<td className="column-category">{getTableProp(category, ['name'])}</td>
					<td className="column-quantity">{getTableProp(quantity)}</td>
					<td className="column-sale_price">{getTableProp(sale_price)}</td>
					<td className="column-purchase_price">{getTableProp(purchase_price)}</td>

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
