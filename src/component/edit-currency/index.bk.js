import React, { Component } from 'react';
import PropTypes from 'prop-types';
import { translate as __ } from 'lib/locale';
import { connect } from 'react-redux';
import notify from 'lib/notify';
import { find } from 'lodash';
import { currencies, getCurrencyDefaults } from 'lib/currency';
import { createItem, updateItem } from 'state/currencies/action';
import { Modal, TextControl, SelectControl, ToggleControl, Icon, Button } from '@eaccounting/components';

const initial = {
	id: undefined,
	name: '',
	code: 'USD',
	rate: '1',
	precision: '2',
	symbol: '$',
	decimalSeparator: '.',
	thousandSeparator: ',',
	position: 'before',
	enabled: true,
};

const currenciesList = Object.keys(currencies).map(code => {
	return {
		label: code,
		value: code,
	};
});

const positions = [
	{
		label: __('Before'),
		value: 'before',
	},
	{
		label: __('After'),
		value: 'after',
	},
];

class EditCurrency extends Component {
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

		this.state = {
			...initial,
			...props.item,
			isSaving: false,
		};
	}

	reset = () => {
		this.setState({
			...initial,
		});
	};

	onChangeCode = code => {
		const defaults = getCurrencyDefaults(code);
		const { precision, symbol, decimalSeparator, thousandSeparator } = defaults;
		this.setState({ code });
		this.setState({ precision });
		this.setState({ symbol });
		this.setState({ decimalSeparator });
		this.setState({ thousandSeparator });
	};

	onSubmit = ev => {
		ev.preventDefault();
		// const {id, name, rate, type = {}, enabled} = this.state;
		// this.setState({isSaving: true});
		// if (name.trim() === '' || rate.trim() === '' || !Object.keys(type).length) {
		// 	this.setState({isSaving: false});
		// 	notify(__('One or more required value missing, please correct & submit again'), 'error');
		// 	return false;
		// }
		//
		//
		// const item = {
		// 	id: parseInt(id, 10),
		// 	name,
		// 	rate,
		// 	type: type.value,
		// 	status: (enabled === true) ? 'active' : 'inactive'
		// };
		//
		// if (item.id) {
		// 	this.props.onSave(item.id, item);
		// } else {
		// 	this.props.onCreate(item);
		// }
		//
		// this.props.onClose ? this.props.onClose(ev) : () => {
		// };
	};

	render() {
		const { tittle = __('Add Currency'), buttonTittle = __('Submit'), onClose } = this.props;
		const { code = 'USD' } = this.state;
		const {
			name,
			rate,
			precision,
			symbol,
			decimalSeparator,
			thousandSeparator,
			position = 'before',
			isSaving,
		} = this.state;

		return (
			<Modal title={tittle} onRequestClose={onClose}>
				<form onSubmit={this.onSubmit}>
					<TextControl
						label={__('Name')}
						value={name}
						before={<Icon icon="id-card-o" />}
						placeholder={__('Enter Name')}
						required
						onChange={name => {
							this.setState({ name });
						}}
					/>

					<SelectControl
						label={__('Code')}
						options={currenciesList}
						value={find(currenciesList, { value: code })}
						before={<Icon icon="code" />}
						required
						onChange={this.onChangeCode}
					/>

					<TextControl
						label={__('Rate')}
						value={rate}
						before={<Icon icon="money" />}
						placeholder={__('Enter Rate')}
						required
						onChange={rate => {
							this.setState({ rate: rate.replace(/[^\d.]+/g, '') });
						}}
					/>

					<TextControl
						label={__('Precision')}
						value={precision}
						before={<Icon icon="bullseye" />}
						placeholder={__('Enter Precision')}
						required
						onChange={precision => {
							this.setState({ precision: precision.replace(/[^\d]+/g, '') });
						}}
					/>

					<TextControl
						label={__('Symbol')}
						value={symbol}
						before={<Icon icon="font" />}
						placeholder={__('Enter Symbol')}
						required
						onChange={symbol => {
							this.setState({ symbol });
						}}
					/>

					<SelectControl
						label={__('Symbol Position')}
						options={positions}
						value={find(positions, { value: position })}
						before={<Icon icon="text-width" />}
						required
						onChange={position => {
							this.setState({ position });
						}}
					/>

					<TextControl
						label={__('Decimal Mark')}
						value={decimalSeparator}
						before={<Icon icon="font" />}
						placeholder={__('Decimal Mark')}
						required
						onChange={decimalSeparator => {
							this.setState({ decimalSeparator });
						}}
					/>

					<TextControl
						label={__('Thousands Separator')}
						value={thousandSeparator}
						before={<Icon icon="columns" />}
						placeholder={__('Enter Thousands Separator')}
						required
						onChange={thousandSeparator => {
							this.setState({ thousandSeparator });
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

function mapDispatchToProps(dispatch) {
	return {
		onSave: (id, item) => {
			dispatch(updateItem(id, item));
		},
		onCreate: item => {
			dispatch(createItem(item));
		},
	};
}

export default connect(null, mapDispatchToProps)(EditCurrency);
