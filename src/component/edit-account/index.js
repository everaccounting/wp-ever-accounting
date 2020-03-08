import React, {Component} from 'react';
import PropTypes from 'prop-types';
import {translate as __} from 'lib/locale';
import notify from 'lib/notify';
import {accountingApi, apiRequest} from "lib/api";
import {Modal, TextControl, PriceControl, TextareaControl, Icon, Button} from '@eaccounting/components';
import CurrencyControl from 'component/currency-control';
import {mergeWith} from "lodash";

export default class EditAccount extends Component {
	static propTypes = {
		item: PropTypes.object,
		onClose: PropTypes.func,
		tittle: PropTypes.string,
		buttonTittle: PropTypes.string,
		childSave: PropTypes.func,
		callback: PropTypes.func,
	};
	static defaultProps = {
		item: {},
	};

	constructor(props) {
		super(props);
		this.state = {
			name: '',
			number: '',
			opening_balance: '',
			bank_name: '',
			bank_phone: '',
			bank_address: '',
			currency: eAccountingi10n.data.currency,
			isSaving: false,
		};
	}

	componentDidMount() {
		const {item} = this.props;

		this.setState({
			...this.state,
			...item
		})
	}


	onSubmit = ev => {
		ev.preventDefault();
		const {id, name, number, opening_balance, bank_name, bank_phone, bank_address, currency} = this.state;
		this.setState({isSaving: true});

		const data = {
			id,
			name,
			number,
			opening_balance,
			bank_name,
			bank_phone,
			bank_address,
			currency_code:currency && currency.code && currency.code
		};

		let endpoint = accountingApi.accounts.create(data);
		if (id) {
			endpoint = accountingApi.accounts.update(id, data);
		}

		apiRequest(endpoint).then(res => {
			notify(__('Account saved successfully'));
			this.props.onCreate && this.props.onCreate(res.data);
			this.setState({isSaving: false});
			this.props.onClose(ev);
		}).catch(error => {
			this.setState({isSaving: false});
			notify(error.message, 'error');
		});

	};

	render() {
		const {tittle = __('Add Account'), buttonTittle = __('Save'), onClose} = this.props;
		const {name, number, opening_balance, bank_name, bank_phone, bank_address, currency = {}, isSaving} = this.state;

		return (
			<Modal title={tittle} onRequestClose={onClose}>
				<form onSubmit={this.onSubmit}>
					<TextControl
						label={__('Account Name')}
						value={name}
						before={<Icon icon="id-card-o"/>}
						required
						onChange={name => {
							this.setState({name});
						}}
					/>

					<TextControl
						label={__('Account Number')}
						value={number}
						before={<Icon icon="pencil"/>}
						required
						onChange={number => {
							this.setState({number});
						}}
					/>
					<CurrencyControl
						label={__('Account Currency')}
						before={<Icon icon="exchange"/>}
						value={currency}
						required
						onChange={currency => {
							this.setState({currency});
						}}
					/>

					<PriceControl
						label={__('Opening Balance')}
						value={opening_balance}
						before={<Icon icon="money"/>}
						code={currency && currency.code || ''}
						required
						onChange={opening_balance => {
							this.setState({opening_balance});
						}}
					/>

					<TextControl
						label={__('Bank Name')}
						value={bank_name}
						before={<Icon icon="university"/>}
						onChange={bank_name => {
							this.setState({bank_name});
						}}
					/>
					<TextControl
						label={__('Bank Phone')}
						value={bank_phone}
						before={<Icon icon="phone"/>}
						onChange={bank_phone => {
							this.setState({bank_phone});
						}}
					/>
					<TextareaControl
						label={__('Bank Address')}
						value={bank_address}
						onChange={bank_address => {
							this.setState({bank_address});
						}}
					/>

					<Button isPrimary isBusy={isSaving} onClick={this.onSubmit}>
						{buttonTittle}
					</Button>
				</form>
			</Modal>
		);
	}
}
