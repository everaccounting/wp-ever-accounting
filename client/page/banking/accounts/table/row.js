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
		const {isLoading, item, isSelected, match} = this.props;
		const {id, name, balance, number} = item;
		console.log(`/${match.path}/${id}/edit`);
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
							onChange={() => this.props.onSetSelected(item.id)}
						/>
					</th>

					<td scope="row" className="column-primary column-name">
						<strong><Link to={`${match.path}/${id}/edit`}>{name}</Link></strong>
					</td>
					<td className="column-number">{number || '-'}</td>
					<td className="column-money">{balance}</td>
					<td className="column-actions">mmm</td>
				</tr>
			</Fragment>
		);
	}
}
