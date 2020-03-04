import {Component, Fragment} from "react";
import PropTypes from "prop-types";
import {translate as __} from 'lib/locale';
import Moment from 'react-moment';
import moment from "moment";

export default class Row extends Component {
	static propTypes = {
		item: PropTypes.object.isRequired,
		disabled: PropTypes.bool.isRequired,
		isSelected: PropTypes.bool,
	};

	constructor(props) {
		super(props);
	}

	getProp = (prop, properties, initial='-') => {
		if( (properties instanceof Object) && properties.hasOwnProperty(prop)){
			return properties[prop];
		}
		return initial;
	};

	render() {
		const {isSelected, disabled, item} = this.props;
		const {paid_at, account, type = '', category = {name: '-'}, reference = '-', amount = ''} = this.props.item;

		return (
			<Fragment>
				<tr className={disabled ? 'disabled' : ''}>
					<td className="column-primary column-date">
						{moment(paid_at).format("d MMM Y")}
					</td>

					<td className="column-primary column-account">
						{this.getProp('name', account)}
					</td>

					<td className="column-primary column-type">
						{type}
					</td>

					<td className="column-primary column-category">
						{this.getProp('name', category)}
					</td>

					<td className="column-primary column-reference">
						{reference}
					</td>

					<td className="column-primary column-amount">
						{amount}
					</td>
				</tr>
			</Fragment>

		)
	}
}
