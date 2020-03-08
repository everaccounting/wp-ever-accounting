/**
 * External dependencies
 */
import { Component, Fragment } from 'react';
import PropTypes from 'prop-types';
import { Column, RowAction, Spinner } from '@eaccounting/components';
import { translate as __ } from 'lib/locale';
import { connect } from 'react-redux';
/**
 * Internal dependencies
 */
import { STATUS_IN_PROGRESS, STATUS_SAVING } from 'status';
import EditAccount from 'component/edit-account';
class Row extends Component {
	static propTypes = {
		item: PropTypes.object.isRequired,
		status: PropTypes.string.isRequired,
		defaultFlags: PropTypes.object,
	};

	constructor(props) {
		super(props);

		this.state = {
			editing: false,
		};
	}

	renderActions(saving) {
		return <RowAction disabled={saving}>{this.getActions()}</RowAction>;
	}

	getActions() {
		const { id } = this.props.item;
		const actions = [];
		actions.push([__('Edit'), this.onEdit]);
		actions.push([__('Delete'), this.onDelete]);
		return actions
			.map((item, pos) => (
				<a key={pos} href={item[2] ? item[2] : '#'} onClick={item[1]}>
					{item[0]}
				</a>
			))
			.reduce((prev, curr) => [prev, ' | ', curr]);
	}

	onEdit = ev => {
		ev.preventDefault();
		this.setState({ editing: !this.state.editing });
	};

	onDelete = ev => {
		ev.preventDefault();
		this.props.onTableAction('delete', this.props.item.id);
	};

	onDisable = ev => {
		ev.preventDefault();
		this.props.onTableAction('disable', this.props.item.id);
	};

	onEnable = ev => {
		ev.preventDefault();
		this.props.onTableAction('enable', this.props.item.id);
	};

	onSelected = () => {
		this.props.onSetSelected([this.props.item.id]);
	};

	onClose = () => {
		this.setState({ editing: !this.state.editing });
	};

	render() {
		const { id, name, balance, number, bank_name } = this.props.item;
		const { editing } = this.state;
		const { status, selected } = this.props;
		const isLoading = status === STATUS_IN_PROGRESS;
		const isSaving = status === STATUS_SAVING;
		const hideRow = isLoading || isSaving;
		return (
			<Fragment>
				<tr className={hideRow ? 'disabled' : ''}>
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

						{editing && (
							<EditAccount
								item={this.props.item}
								onClose={this.onClose}
								buttonTittle={__('Update')}
								tittle={__('Update Account')}
							/>
						)}
					</th>

					<Column className="column-primary column-name">
						<strong>
							<a href="#" onClick={this.onEdit}>
								{name}
							</a>
						</strong>
						{this.renderActions(isSaving)}
					</Column>

					<Column className="column-number">{number || '-'}</Column>

					<Column className="column-balance ea-money">{balance}</Column>
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
