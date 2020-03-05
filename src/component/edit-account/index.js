import React, { Component } from 'react';
import PropTypes from 'prop-types';
import { translate as __ } from 'lib/locale';
import { connect } from 'react-redux';
import notify from 'lib/notify';
import { Modal, TextControl, PriceControl, TextareaControl, Icon, Button } from '@eaccounting/components';
import CurrencyControl from 'component/currency-control';
import { setCreateItem, setUpdateItem } from 'state/categories/action';

const initial = {
	name: '',
	number: '',
	opening_balance: '0',
	bank_name: '',
	bank_phone: '',
	bank_address: '',
	currency: eAccountingi10n.default_currency,
};

class EditAccount extends Component {
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
			...initial,
			...props.item,
			isSaving: false,
		};
	}

	reset = () => {
		this.setState({
			...initialAccount,
		});
	};

	onSubmit = ev => {};

	render() {
		const { tittle = __('Add Account'), buttonTittle = __('Save'), onClose } = this.props;
		const { name, number, opening_balance, bank_name, bank_phone, bank_address, currency, isSaving } = this.state;

		return (
			<form onSubmit={this.onSave} ref={this.ref}>
				<Modal title={tittle} onRequestClose={onClose}>
					<form onSubmit={this.onSubmit}>
						<TextControl
							label={__('Account Name')}
							value={name}
							before={<Icon icon="id-card-o" />}
							required
							onChange={name => {
								this.setState({ name });
							}}
						/>

						<TextControl
							label={__('Account Number')}
							value={number}
							before={<Icon icon="pencil" />}
							required
							onChange={number => {
								this.setState({ number });
							}}
						/>
						<CurrencyControl
							label={__('Account Currency')}
							selected={currency.id}
							required
							onChange={currency => {
								this.setState({ currency });
							}}
						/>
						<PriceControl
							label={__('Opening Balance')}
							value={opening_balance}
							before={<Icon icon="money" />}
							currency={currency}
							required
							onChange={opening_balance => {
								this.setState({ opening_balance });
							}}
						/>

						<TextControl
							label={__('Bank Name')}
							value={bank_name}
							before={<Icon icon="university" />}
							onChange={bank_name => {
								this.setState({ bank_name });
							}}
						/>
						<TextControl
							label={__('Bank Phone')}
							value={bank_phone}
							before={<Icon icon="phone" />}
							onChange={bank_phone => {
								this.setState({ bank_phone });
							}}
						/>
						<TextareaControl
							label={__('Bank Address')}
							value={bank_address}
							onChange={bank_address => {
								this.setState({ bank_address });
							}}
						/>

						<Button isPrimary isBusy={isSaving} onClick={this.onSubmit}>
							{buttonTittle}
						</Button>
					</form>
				</Modal>
			</form>
		);
	}
}

function mapDispatchToProps(dispatch) {
	return {
		onSave: (id, item) => {
			dispatch(setCreateItem(id, item));
		},
		onCreate: item => {
			dispatch(setUpdateItem(item));
		},
	};
}

export default connect(null, mapDispatchToProps)(EditAccount);
