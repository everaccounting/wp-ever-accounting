/**
 * External dependencies
 */

import { Component, Fragment } from 'react';
import { connect } from 'react-redux';
import PropTypes from 'prop-types';
/**
 * Internal dependencies
 */
import { setSelected, performTableAction, updateContact } from 'state/accounts/action';
import { STATUS_SAVING, STATUS_IN_PROGRESS } from 'status';
import Spinner from 'component/spinner';
import Column from 'component/table/column';
import RowActions from 'component/table/row-action';
import EditContact from 'component/edit-account';
import { translate as __ } from 'lib/locale';
class ContactsRow extends Component {
	static propTypes = {
		item: PropTypes.object.isRequired,
		selected: PropTypes.bool.isRequired,
		rowstatus: PropTypes.string.isRequired,
		defaultFlags: PropTypes.object,
	};

	constructor(props) {
		super(props);

		this.state = {
			editing: false,
		};
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

	getActions() {
		const { id, enabled } = this.props.item;
		const actions = [];
		actions.push([__('Edit'), this.onEdit]);
		actions.push([__('Delete'), this.onDelete]);
		if (enabled) {
			actions.push([__('Disable'), this.onDisable]);
		} else {
			actions.push([__('Enable'), this.onEnable]);
		}

		return actions
			.map((item, pos) => (
				<a key={pos} href={item[2] ? item[2] : '#'} onClick={item[1]}>
					{item[0]}
				</a>
			))
			.reduce((prev, curr) => [prev, ' | ', curr]);
	}

	renderActions(saving) {
		return <RowActions disabled={saving}>{this.getActions()}</RowActions>;
	}

	render() {
		const { id, first_name, last_name, email, phone, enabled } = this.props.item;
		const { selected, rowstatus, currentDisplaySelected } = this.props;
		const isLoading = rowstatus === STATUS_IN_PROGRESS;
		const isSaving = rowstatus === STATUS_SAVING;
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
					</th>

					<Column enabled="name" className="column-primary column-name" selected={currentDisplaySelected}>
						<strong>
							<a href="#" onClick={this.onEdit}>{`${first_name} ${last_name}`}</a>
						</strong>
						{this.renderActions(isSaving)}
					</Column>

					<Column enabled="email" className="column-email" selected={currentDisplaySelected}>
						{email || '-'}
					</Column>

					<Column enabled="phone" className="column-phone" selected={currentDisplaySelected}>
						{phone || '-'}
					</Column>
					<Column enabled="status" className="column-status" selected={currentDisplaySelected}>
						{enabled ? (
							<span className="ea-item-status enabled">{__('Enabled')}</span>
						) : (
							<span className="ea-item-status disabled">{__('Disabled')}</span>
						)}
						{this.state.editing && (
							<EditContact
								item={this.props.item}
								tittle={__('Update Contact')}
								buttonTittle={__('Update')}
								onClose={this.onClose}
							/>
						)}
					</Column>
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
		onSaveContact: (id, item) => {
			dispatch(updateContact(id, item));
		},
		onTableAction: (action, ids) => {
			dispatch(performTableAction(action, ids));
		},
	};
}

function mapStateToProps(state) {
	const { accounts } = state;

	return {
		accounts,
	};
}

export default connect(mapStateToProps, mapDispatchToProps)(ContactsRow);
