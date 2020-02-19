/**
 * External dependencies
 */

import {Component} from 'react';
import {translate as __} from 'lib/locale';
import {connect} from 'react-redux';
import PropTypes from 'prop-types';
import {find} from 'lodash';
import {
	Modal,
	TextControl,
	PriceControl,
	TextareaControl,
	ToggleControl,
	Icon
} from '@eaccounting/components';
import CurrencyControl from 'component/currency-control';

/**
 * Internal dependencies
 */
import {setSelected, performTableAction, updateAccount} from 'state/accounts/action';
import {STATUS_SAVING, STATUS_IN_PROGRESS} from 'lib/status';
import Spinner from 'component/spinner';
import Column from 'component/table/column';
import RowActions from 'component/table/row-action';

class AccountsRow extends Component {
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
			name: props.item.name,
			number: props.item.number,
			opening_balance: props.item.opening_balance,
			bank_name: props.item.bank_name,
			bank_phone: props.item.bank_phone,
			bank_address: props.item.bank_address,
			currency: props.item.currency,
			currencies: [],
			enabled: props.item.enabled,
			isSaving: false,
		};
	}

	onEdit = ev => {
		ev.preventDefault();
		this.setState({editing: !this.state.editing});
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

	onSubmit = ev => {
		ev.preventDefault();
		this.setState({isSaving: true});
		if (!this.state.name || !this.state.number || !this.state.currency) {
			this.setState({isSaving: false});
			notify(__('One or more required value missing, please correct & submit again'), 'error');
			return false;
		}
		let status = (this.state.enabled === true) ? 'active' : 'inactive';
		console.log(this.state);
		this.props.onSaveAccount(this.props.item.id, {
			name: this.state.name,
			number: this.state.number,
			opening_balance: this.state.opening_balance,
			currency_code: this.state.currency.code,
			bank_name: this.state.bank_name,
			bank_phone: this.state.bank_phone,
			bank_address: this.state.bank_address,
			status
		});
		this.onClose();
	};

	onClose = () => {
		this.setState({editing: !this.state.editing});
	};

	getActions() {
		const {id, enabled} = this.props.item;
		const actions = [];
		actions.push([__('Edit'), this.onEdit]);
		actions.push([__('Delete'), this.onDelete]);
		if (enabled) {
			actions.push([__('Disable'), this.onDisable]);
		} else {
			actions.push([__('Enable'), this.onEnable]);
		}

		return actions
			.map((item, pos) => <a key={pos} href={item[2] ? item[2] : '#'} onClick={item[1]}>{item[0]}</a>)
			.reduce((prev, curr) => [prev, ' | ', curr]);
	}

	renderActions(saving) {
		return (
			<RowActions disabled={saving}>
				{this.getActions()}
			</RowActions>
		);
	}

	getEditModal() {
		const {
			currency,
			isSaving
		} = this.state;
		return (
			<Modal title={__('Edit Account')} onRequestClose={this.onClose}>
				<form onSubmit={this.onSubmit}>
					<TextControl label={__('Account Name')}
								 value={this.state.name}
								 before={<Icon icon='id-card-o'/>}
								 required
								 onChange={(name) => {
									 this.setState({name})
								 }}/>

					<TextControl label={__('Account Number')}
								 value={this.state.number}
								 before={<Icon icon='pencil'/>}
								 required
								 onChange={(number) => {
									 this.setState({number})
								 }}/>

					<CurrencyControl label={__('Account Currency')}
									 value={currency}
									 required
									 onChange={(currency) => {
										 this.setState({currency})
									 }}/>

					<PriceControl label={__('Opening Balance')}
								  value={this.state.opening_balance}
								  before={<Icon icon='money'/>}
								  currency={currency}
								  required
								  onChange={(opening_balance) => {
									  this.setState({opening_balance})
								  }} options={this.state.currencies}/>
					<TextControl label={__('Bank Name')}
								 value={this.state.bank_name}
								 before={<Icon icon='university'/>}
								 onChange={(bank_name) => {
									 this.setState({bank_name})
								 }}/>
					<TextControl label={__('Bank Phone')}
								 value={this.state.bank_phone}
								 before={<Icon icon='phone'/>}
								 onChange={(bank_phone) => {
									 this.setState({bank_phone})
								 }}/>
					<TextareaControl label={__('Bank Address')}
									 value={this.state.bank_address}
									 onChange={(bank_address) => {
										 this.setState({bank_address})
									 }}/>
					<ToggleControl label={__('Enabled')}
								   checked={this.state.enabled}
								   onChange={() => {
									   this.setState({enabled: !this.state.enabled})
								   }}/>

					<input className="button-primary" type="submit" name="update" value={__('Update Account')}
						   disabled={isSaving || this.state.name === ''}/>
				</form>
			</Modal>
		)
	}

	render() {
		const {id, name, balance, number, bank_name, enabled} = this.props.item;
		const {selected, rowstatus, currentDisplaySelected} = this.props;
		const isLoading = rowstatus === STATUS_IN_PROGRESS;
		const isSaving = rowstatus === STATUS_SAVING;
		const hideRow = isLoading || isSaving;
		return (
			<tr className={hideRow ? 'disabled' : ''}>

				<th scope="row" className="check-column">
					{!isSaving &&
					<input type="checkbox" name="item[]" value={id} disabled={isLoading} checked={selected}
						   onChange={this.onSelected}/>}
					{isSaving && <Spinner size="small"/>}
				</th>

				<Column enabled="name" className="column-primary column-name" selected={currentDisplaySelected}>
					<strong><a href="#" onClick={this.onEdit}>{name}</a></strong>
					{this.renderActions(isSaving)}
				</Column>

				<Column enabled="balance" className="column-balance" selected={currentDisplaySelected}>
					<span className='ea-money'>{balance}</span>
				</Column>

				<Column enabled="number" className="column-number" selected={currentDisplaySelected}>
					{number}
				</Column>

				<Column enabled="bank_name" className="column-bank-name" selected={currentDisplaySelected}>
					{bank_name}
				</Column>

				<Column enabled="status" className="column-status" selected={currentDisplaySelected}>
					{enabled ? <span className='ea-item-status enabled'>{__('Enabled')}</span> :
						<span className='ea-item-status disabled'>{__('Disabled')}</span>}
				</Column>

				{this.state.editing && this.getEditModal()}
			</tr>
		)
	}
}


function mapDispatchToProps(dispatch) {
	return {
		onSetSelected: items => {
			dispatch(setSelected(items));
		},
		onSaveAccount: (id, item) => {
			dispatch(updateAccount(id, item));
		},
		onTableAction: (action, ids) => {
			dispatch(performTableAction(action, ids));
		},
	};
}

function mapStateToProps(state) {
	const {accounts} = state;

	return {
		accounts,
	};
}

export default connect(
	mapStateToProps,
	mapDispatchToProps,
)(AccountsRow);
