import {Component, Fragment} from 'react';
import PropTypes from 'prop-types';
import {__} from '@wordpress/i18n';
import moment from 'moment';

export default class Row extends Component {
	static propTypes = {
		item: PropTypes.object.isRequired,
		isLoading: PropTypes.bool.isRequired,
		isSelected: PropTypes.bool,
	};

	constructor(props) {
		super(props);

		this.state = {
			editing: false,
		};
	}

	onEdit = () => {
		this.setState({editing: !this.state.editing});
	};

	onSelected = () => {
		this.props.onSetSelected([this.props.item.id]);
	};

	onClose = () => {
		this.setState({editing: !this.state.editing});
	};

	OnSave = (item) => {
		this.props.onUpdate(item);
	};

	render() {
		const {isSelected, isLoading, item} = this.props;
		const {paid_at, account, type, category, reference, amount} = this.props.item;
		return (
			<Fragment>
				<tr className={isLoading ? 'disabled' : ''}>
					<td className="column-primary column-date">{moment(paid_at).format('d MMM Y')}</td>
					<td className="column-amount">{amount}</td>
					<td className="column-account">{account && account.name && account.name || '-'}</td>
					<td className="column-type ea-capitalize">{type}</td>
					<td className="column-category">{category && category.name && category.name || '-'}</td>
					<td className="column-reference">{reference}</td>
				</tr>
			</Fragment>
		);
	}
}

