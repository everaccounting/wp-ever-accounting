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
		const {isLoading, item, match, getTableProp} = this.props;
		const {id, paid_at, account, type, category, reference, amount} = this.props.item;
		return (
			<Fragment>
				<tr className={isLoading ? 'disabled' : ''}>
					<td scope="row" className="column-primary column-date">
						<Link to={`${match.path}/${id}/edit`}>{moment(paid_at).format(FORMAT_SITE_DATE)}</Link>
					</td>

					<td className="column-amount">{getTableProp(amount)}</td>
					<td className="column-account">{getTableProp(account, ['name'])}</td>
					<td className="column-type ea-capitalize">{getTableProp(type)}</td>
					<td className="column-category">{getTableProp(category, ['name'])}</td>
					<td className="column-reference">{getTableProp(reference)}</td>

				</tr>
			</Fragment>
		);
	}
}
