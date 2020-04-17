import {Component, Fragment} from 'react';
import PropTypes from 'prop-types';
import {RowActions} from '@eaccounting/components';
import {__} from '@wordpress/i18n';
import {Link} from "react-router-dom";
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
		const {id, name, balance, number} = item;
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
						<Link to={`${match.path}/${id}/edit`}>{this.props.getTableProp(name)}</Link>
					</td>
					<td className="column-number">{this.props.getTableProp(number)}</td>
					<td className="column-money">{this.props.getTableProp(balance)}</td>
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
