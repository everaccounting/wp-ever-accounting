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
		const { id} = item;
		const {history} = this.props;
		const {name, sku, sale_price, purchase_price, quantity, image, category} = item;
		return (
			<Fragment>
				<tr className={isLoading ? 'disabled' : ''}>
					<th scope="row" className="column-primary column-image">
						<Link to={`${history.location.pathname}/edit/${id}`}>{name}</Link>
					</th>

					<td className="column-name">
						<Link to={`${history.location.pathname}/edit/${id}`}>{name}</Link>
					</td>

					<td className="column-category">
						{category && category.name && category.name || '&mdash;'}
					</td>

					<td className="column-quantity">
						{quantity || '&mdash;'}
					</td>

					<td className="column-sale_price">
						{sale_price || '&mdash;'}
					</td>

					<td className="column-purchase_price">
						{purchase_price || '&mdash;'}
					</td>

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
