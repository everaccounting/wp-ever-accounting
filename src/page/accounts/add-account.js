/**
 * External dependencies
 */

import {Component} from 'react';
import {translate as __} from 'lib/locale';
import PropTypes from 'prop-types';
import {connect} from 'react-redux';
/**
 * Internal dependencies
 */
import './style.scss';
import {
	Modal,
	TextControl,
	PriceControl,
	TextareaControl,
	ToggleControl,
	Icon
} from '@eaccounting/components';
import CurrencyControl from 'component/currency-control';
import {createAccount} from 'state/accounts/action'
import notify from "lib/notify";

class AddAccount extends Component {
	constructor(props) {
		super(props);
		this.state = {
			name: '',
			number: '',
			opening_balance: '',
			bank_name: '',
			bank_phone: '',
			bank_address: '',
			currency: eAccountingi10n.default_currency,
			enabled: true,
			isSaving: false,
		};
	}

	onSubmit = ev => {
		ev.preventDefault();
		this.setState({isSaving: true});
		if (!this.state.name || !this.state.number || !this.state.currency) {
			this.setState({isSaving: false});
			notify(__('One or more required value missing, please correct & submit again'), 'error');
			return false;
		}
		this.props.onCreate({
			id: 0,
			name: this.state.name,
			number: this.state.number,
			opening_balance: this.state.opening_balance,
			currency_code: this.state.currency.code,
			bank_name: this.state.bank_name,
			bank_phone: this.state.bank_phone,
			bank_address: this.state.bank_address,
			status: (this.state.enabled === true) ? 'active' : 'inactive'
		});
		this.props.onClose();
	};

	render() {
		const {
			currency,
			isSaving
		} = this.state;
		return (
			<Modal title={__('Add Account')} onRequestClose={this.props.onClose}>
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
					<input className="button-primary" type="submit" name="add" value={__('Add Account')}
						   disabled={isSaving || this.state.name === ''}/>
				</form>
			</Modal>
		)
	}
}

AddAccount.propTypes = {
	onClose: PropTypes.func,
};


function mapDispatchToProps(dispatch) {
	return {
		onCreate: item => {
			dispatch(createAccount(item));
		},
	}
}

export default connect(
	null,
	mapDispatchToProps,
)(AddAccount);
