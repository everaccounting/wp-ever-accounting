import React, { Component, Fragment } from 'react';
import PropTypes from 'prop-types';
import { translate as __ } from 'lib/locale';
import { connect } from 'react-redux';
import notify from 'lib/notify';
// import {currencies, getCurrencyDefaults} from "lib/currency";
import { Modal, TextControl, SelectControl, Icon, Button } from '@eaccounting/components';

import { apiRequest, accountingApi } from 'lib/api';
// import {getSelectedOption} from "lib/table";

const initial = {
	id: undefined,
	name: '',
	code: 'USD',
	rate: '1',
	precision: '2',
	position: 'before',
};

// const currenciesOptions = Object.keys(currencies).map((code) => {
// 	return {
// 		label: code,
// 		value: code,
// 	}
// });

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

	componentWillUnmount() {
		this.reset();
	}

	reset = () => {
		this.setState({
			...initial,
		});
	};

	onChangeCode = code => {
		this.setState({ code: code.value });
	};

	onSubmit = ev => {
		ev.preventDefault();
		const { id, name, rate, code, precision, position } = this.state;
		this.setState({ isSaving: true });

		const item = {
			id: parseInt(id, 10),
			name,
			code,
			rate,
			precision,
			position,
		};

		if (item.id) {
			this.props.onSave(item.id, item);
			return this.props.onClose(ev);
		}
		apiRequest(accountingApi.currencies.create(item))
			.then(res => {
				notify(__('Currency created successfully'));
				this.props.onCreate(res.data);
				this.setState({ isSaving: false });
				this.props.onClose(ev);
			})
			.catch(error => {
				this.setState({ isSaving: false });
				notify(error.message, 'error');
			});
	};

	render() {
		const { tittle = __('Add Currency'), buttonTittle = __('Submit'), onClose } = this.props;
		const { name, rate, code, precision, position, isSaving } = this.state;
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
						options={currenciesOptions}
						value={getSelectedOption(currenciesOptions, code, 'USD')}
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
						help={__('Rate against default currency')}
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

					<SelectControl
						label={__('Symbol Position')}
						options={positions}
						value={getSelectedOption(positions, position, 'after')}
						before={<Icon icon="text-width" />}
						required
						onChange={position => {
							this.setState({ position: position.value });
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
			dispatch(setUpdateItem(id, item));
		},
		onCreate: item => {
			dispatch(setCreateItem(item));
		},
	};
}

export default connect(null, mapDispatchToProps)(EditCurrency);
