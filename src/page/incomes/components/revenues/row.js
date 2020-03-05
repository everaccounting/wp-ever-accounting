import { Component, Fragment } from 'react';
import PropTypes from 'prop-types';

import { setSelected, setBulkAction, setUpdateItem } from 'state/categories/action';
import { STATUS_SAVING, STATUS_IN_PROGRESS } from 'status';
import { RowAction, Column, Spinner, Icon, RowActions } from '@eaccounting/components';
import { translate as __ } from 'lib/locale';
import { connect } from 'react-redux';
import Moment from 'react-moment';
import { Link } from 'react-router-dom';

class Row extends Component {
	static propTypes = {
		item: PropTypes.object.isRequired,
		// selected: PropTypes.bool.isRequired,
		status: PropTypes.string.isRequired,
	};

	constructor(props) {
		super(props);

		this.state = {
			editing: false,
		};
	}

	onEdit = () => {
		console.log('edit');
		// this.setState({editing: !this.state.editing});
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
		const { id, paid_at, amount, account, contact, category } = this.props.item;
		const { editing } = this.state;
		const { status, selected } = this.props;
		const isLoading = status === STATUS_IN_PROGRESS;
		const isSaving = status === STATUS_SAVING;
		const disabled = isLoading || isSaving;
		const { match } = this.props;
		return (
			<Fragment>
				<tr className={disabled ? 'disabled' : ''}>
					<th scope="row" className="check-column">
						{!isSaving && (
							<input
								type="checkbox"
								name="item[]"
								value={id}
								disabled={isLoading}
								checked={selected}
								onChange={this.onSelected}
							/>
						)}
						{isSaving && <Spinner size="small" />}
					</th>

					<td className="column-primary column-paid_at">
						<Link to={`${match.path}/${id}`}>
							<Moment format={'DD-MM-YYYY'}>{paid_at}</Moment>
						</Link>
					</td>

					<td className="column-amount">{amount}</td>

					<td className="column-category"></td>

					<td className="column-account"></td>

					<td className="column-customer"></td>

					<td className="column-actions">
						<RowActions
							disabled={disabled}
							actions={[
								{
									title: __('Edit'),
									onClick: this.onEdit,
								},
								{
									title: __('Delete'),
									onClick: this.onEdit,
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
		onSetSelected: items => {
			dispatch(setSelected(items));
		},
		onSaveCategory: (id, item) => {
			dispatch(setUpdateItem(id, item));
		},
		onTableAction: (action, ids) => {
			dispatch(setBulkAction(action, ids));
		},
	};
}

export default connect(null, mapDispatchToProps)(Row);
