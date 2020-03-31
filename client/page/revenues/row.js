import {Component, Fragment} from '@wordpress/element';
import PropTypes from 'prop-types';
import {__} from '@wordpress/i18n';
import {RowActions} from "@eaccounting/components";
import { Link } from 'react-router-dom';
import Moment from 'react-moment';

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
		const {isSelected, isLoading, item, search} = this.props;
		const {id, paid_at, amount, account, contact, category} = item;
		const {match} = this.props;
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
							onChange={() => this.props.onSelected(id)}/>

					</th>

					<td className="column-primary column-paid_at">
						<Link to={`${match.url}/${id}`}>
							<Moment format={'DD-MM-YYYY'}>{paid_at}</Moment>
						</Link>
					</td>

					<td className="column-amount">{amount}</td>

					<td className="column-category">{category && category.name ? category.name : '-'}</td>

					<td className="column-account">{account && account.name ? account.name : '-'}</td>

					<td className="column-customer">{contact && contact.first_name ? `${contact.first_name} ${contact.last_name}` : '-'}</td>


					<td className="column-actions">
						<RowActions
							controls={[
								{
									title: __('Edit'),
									onClick: this.onEdit,
									disabled: isLoading,
								},
								{
									title: __('Delete'),
									onClick: this.onEdit,
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

