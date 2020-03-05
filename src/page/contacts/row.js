import { Component, Fragment } from 'react';
import PropTypes from 'prop-types';
import { RowActions } from '@eaccounting/components';
import { translate as __ } from 'lib/locale';
import { connect } from 'react-redux';
import Moment from 'react-moment';
import { Link } from 'react-router-dom';

class Row extends Component {
	static propTypes = {
		item: PropTypes.object.isRequired,
		disabled: PropTypes.bool.isRequired,
		isSelected: PropTypes.bool,
	};

	constructor(props) {
		super(props);

		this.state = {
			editing: false,
		};
	}

	onEdit = () => {
		console.log('edit');
	};

	onDelete = ev => {
		ev.preventDefault();
		this.props.onTableAction('delete', this.props.item.id);
	};

	onSelected = () => {
		this.props.onSetSelected([this.props.item.id]);
	};

	onClose = () => {
		this.setState({ editing: !this.state.editing });
	};

	render() {
		const { isSelected, disabled, item } = this.props;
		const { id, first_name, last_name, email, phone } = item;
		const { editing } = this.state;
		const { match } = this.props;
		return (
			<Fragment>
				<tr className={disabled ? 'disabled' : ''}>
					<th scope="row" className="check-column">
						<input
							type="checkbox"
							name="item[]"
							value={id}
							disabled={disabled}
							checked={isSelected}
							onChange={() => this.props.onSetSelected(item.id)}
						/>
					</th>

					<td className="column-name">
						<a href="#" onClick={this.onEdit}>{`${first_name} ${last_name}`}</a>
					</td>

					<td className="column-email">{email}</td>

					<td className="column-phone">{phone}</td>

					<td className="column-actions">
						<RowActions
							controls={[
								{
									title: __('Edit'),
									onClick: this.onEdit,
									disabled: disabled,
								},
								{
									title: __('Delete'),
									onClick: this.onEdit,
									disabled: disabled,
								},
							]}
						/>
					</td>
				</tr>
			</Fragment>
		);
	}
}

function mapDispatchToProps(dispatch) {
	return {
		onSetSelected: ids => {
			dispatch({ type: 'CONTACTS_SELECTED', ids: [ids] });
		},
	};
}

export default connect(null, mapDispatchToProps)(Row);
