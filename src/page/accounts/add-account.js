/**
 * External dependencies
 */

import {Component, Fragment} from 'react';
import {translate as __} from 'lib/locale';
import PropTypes from 'prop-types';
import {Button} from "@wordpress/components";
import {find} from 'lodash';
import {connect} from 'react-redux';
/**
 * Internal dependencies
 */
import './style.scss';
import {
	Modal,
	TextControl,
	ReactSelect,
	CurrencyControl,
	TextareaControl,
	ToggleControl,
	Icon
} from '@eaccounting/components';
import {eAccountingApi, getApi} from "lib/api";
import {createAccount} from 'state/accounts/action'
import notify from "lib/notify";

class AddAccount extends Component {
	constructor(props) {
		super(props);
		this.state = {
			currencies: [eAccountingi10n.default_currency],
			options: [],
			name: '',
			number: '',
			opening_balance: '',
			currency_code: eAccountingi10n.default_currency.code,
			currency: undefined,
			bank_name: '',
			bank_phone: '',
			bank_address: '',
			status: true,
			isSaving: false,
		};
		window.addEventListener('popstate', this.onPageChanged);
	}

	componentDidCatch(error, info) {
		this.setState({error: true, stack: error, info});
	}

	componentDidMount() {
		getApi(eAccountingApi.currencies.list({per_page: 100})).then(res => {
			this.setState({
				options: res.items.map((currency) => {
					return {
						label: currency.name,
						value: currency.code
					}
				}),
				currencies: res.items
			});
		})
	}

	componentWillUnmount() {
		window.removeEventListener('popstate', this.onPageChanged);
	}

	onSubmit = ev => {
		ev.preventDefault();
		this.setState({isSaving: true});
		const {name, number, opening_balance, currency_code, bank_name, bank_phone, bank_address, status} = this.state;
		if (!name || !number || !currency_code) {
			this.setState({isSaving: false});
			notify(__('One or more required value missing, please correct & submit again'), 'error');
			return false;
		}
		let enabled = (status === true) ? 'active' : 'inactive';
		const item = {
			name,
			number,
			opening_balance,
			currency_code,
			bank_name,
			bank_phone,
			bank_address,
			status: enabled
		};
		console.log(item);
		this.props.onCreate(item);
		this.props.onClose();
	};

	render() {
		const {
			currency = {
				label: eAccountingi10n.default_currency.name,
				value: eAccountingi10n.default_currency.code
			},
			currencies,
			isSaving
		} = this.state;

		const currencyCode = currency.value;
		const selectedCurrency = find(currencies, {code: currencyCode});
		const {precision = 2, symbol = '$', decimal_mark = '.', thousands_separator = '', rate = '1', symbol_position = 'before'} = selectedCurrency;

		return (
			<Modal title={__('Add Account')} onRequestClose={this.props.onClose}>
				<form onSubmit={this.onSubmit}>
					<TextControl label={__('Name')}
								 value={this.state.name}
								 before={<Icon icon='id-card-o'/>}
								 required
								 onChange={(name) => {
									 this.setState({name})
								 }}/>

					<TextControl label={__('Number')}
								 value={this.state.number}
								 before={<Icon icon='pencil'/>}
								 required
								 onChange={(number) => {
									 this.setState({number})
								 }}/>

					<ReactSelect label={__('Currency')}
								 value={currency}
								 before={<Icon icon='exchange'/>}
								 required
								 onChange={(currency) => {
									 this.setState({currency})
								 }} options={this.state.options}/>

					<CurrencyControl label={__('Opening Balance')}
									 value={this.state.opening_balance}
									 before={<Icon icon='money'/>}
									 precision={precision}
									 symbol={symbol}
									 decimal_mark={decimal_mark}
									 thousands_separator={thousands_separator}
									 symbol_position={symbol_position}
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
								   checked={this.state.status}
								   onChange={() => {
									   this.setState({status: !this.state.status})
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
