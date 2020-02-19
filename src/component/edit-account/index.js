import React, {Component} from 'react';
import PropTypes from 'prop-types';
import {translate as __} from 'lib/locale';
import {connect} from 'react-redux';
import notify from "lib/notify";
import {
	Modal,
	TextControl,
	PriceControl,
	TextareaControl,
	ToggleControl,
	Icon
} from '@eaccounting/components';
import CurrencyControl from 'component/currency-control';
import {createAccount, updateAccount} from 'state/accounts/action'
import {initialAccount} from 'state/accounts/selection';

class EditAccount extends Component {
	static propTypes = {
		item: PropTypes.object,
		onClose: PropTypes.func,
		tittle: PropTypes.string,
		buttonTittle: PropTypes.string,
		childSave: PropTypes.func,
		callback: PropTypes.func,
	};

	constructor(props) {
		super(props);
		const {name, number, opening_balance, bank_name, bank_phone, currency_code, bank_address, currency = eAccountingi10n.default_currency, enabled = true} = props.item;
		this.state = {
			name,
			number,
			opening_balance,
			bank_name,
			bank_phone,
			currency_code,
			bank_address,
			currency,
			enabled,
			isSaving: false,
		};
		this.ref = React.createRef();
	}

	reset = () => {
		this.setState( {
			... initialAccount,
		} );
	};

	onSubmit = ev => {
		ev.preventDefault();
		const {
			name,
			number,
			opening_balance,
			bank_name,
			bank_phone,
			bank_address,
			currency,
			enabled
		} = this.state;

		if (name === '' || number === '' || !currency) {
			this.setState({isSaving: false});
			notify(__('One or more required value missing, please correct & submit again'), 'error');
			return false;
		}

		const item = {
			id: parseInt(this.props.item.id, 10),
			name,
			number,
			opening_balance,
			bank_name,
			bank_phone,
			currency_code: currency.code,
			bank_address,
			status: (enabled === true) ? 'active' : 'inactive'
		};

		if (item.id) {
			this.props.onSave(item.id, item);
		} else {
			this.props.onCreate(item);
		}

		this.props.onClose ? this.props.onClose(ev) : () => {
		};

		if (this.props.childSave) {
			this.props.childSave();
		}

	};

	render() {
		const {tittle = __('Add Account'), buttonTittle = __('Save'), onClose} = this.props;
		const {
			name,
			number,
			opening_balance,
			bank_name,
			bank_phone,
			bank_address,
			currency,
			enabled,
			isSaving
		} = this.state;

		return (
			<form onSubmit={this.onSave} ref={this.ref}>
				<Modal title={tittle} onRequestClose={onClose}>
					<form onSubmit={this.onSubmit}>

						<TextControl label={__('Account Name')}
									 value={name}
									 before={<Icon icon='id-card-o'/>}
									 required
									 onChange={(name) => {
										 this.setState({name})
									 }}/>

						<TextControl label={__('Account Number')}
									 value={number}
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
									  value={opening_balance}
									  before={<Icon icon='money'/>}
									  currency={currency}
									  required
									  onChange={(opening_balance) => {
										  this.setState({opening_balance})
									  }}/>

						<TextControl label={__('Bank Name')}
									 value={bank_name}
									 before={<Icon icon='university'/>}
									 onChange={(bank_name) => {
										 this.setState({bank_name})
									 }}/>
						<TextControl label={__('Bank Phone')}
									 value={bank_phone}
									 before={<Icon icon='phone'/>}
									 onChange={(bank_phone) => {
										 this.setState({bank_phone})
									 }}/>
						<TextareaControl label={__('Bank Address')}
										 value={bank_address}
										 onChange={(bank_address) => {
											 this.setState({bank_address})
										 }}/>
						<ToggleControl label={__('Enabled')}
									   checked={enabled}
									   onChange={() => {
										   this.setState({enabled: !this.state.enabled})
									   }}/>
						{this.props.children && this.props.children}
						<input className="button-primary" type="submit" name="add" value={buttonTittle}
							   disabled={isSaving || name === ''}/>
					</form>
				</Modal>
			</form>
		)
	}
}

function mapDispatchToProps(dispatch) {
	return {
		onSave: (id, item) => {
			dispatch(updateAccount(id, item));
		},
		onCreate: item => {
			dispatch(createAccount(item));
		}
	};
}

export default connect(
	null,
	mapDispatchToProps,
)(EditAccount);

