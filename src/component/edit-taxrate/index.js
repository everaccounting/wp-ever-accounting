import React, { Component } from 'react';
import PropTypes from 'prop-types';
import { translate as __ } from 'lib/locale';
import notify from 'lib/notify';
import { Modal, TextControl, Select, Icon, Button } from '@eaccounting/components';
import { apiRequest, accountingApi } from 'lib/api';
import {isEmpty} from "lodash";

export default class EditTaxRate extends Component {
	static propTypes = {
		item: PropTypes.object,
		onClose: PropTypes.func,
		onCreate: PropTypes.func,
		tittle: PropTypes.string,
		buttonTittle: PropTypes.string,
	};

	constructor(props) {
		super(props);

		this.state = {
			id: null,
			name: '',
			rate: '',
			type: 'normal',
		};
	}

	componentDidMount() {
		const {item} = this.props;
		item && this.setState({
			...this.state,
			...item,
		})
	}

	onChangeType = type => {
		this.setState({ type: type.value });
	};

	onSubmit = ev => {
		ev.preventDefault();
		const { id, name, rate, type } = this.state;
		this.setState({ isSaving: true });

		const data = {
			id,
			name,
			rate,
			type,
		};

		let endpoint = accountingApi.taxrates.create(data);
		if (id) {
			endpoint = accountingApi.taxrates.update(id, data);
		}

		apiRequest(endpoint).then(res => {
			notify(__('Tax Rates saved successfully'));
			this.props.onCreate && this.props.onCreate(res.data);
			this.setState({isSaving: false});
			this.props.onClose(ev);
		}).catch(error => {
			this.setState({isSaving: false});
			notify(error.message, 'error');
		});
	};

	render() {
		const { tittle = __('Add Tax Rate'), buttonTittle = __('Submit'), onClose } = this.props;
		const { name, rate, type, isSaving } = this.state;

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

					<TextControl
						label={__('Rate')}
						value={rate}
						before={<Icon icon="percent" />}
						placeholder={__('Enter Rate')}
						required
						onChange={rate => {
							this.setState({ rate: rate.replace(/[^\d.]+/g, '') });
						}}
					/>

					<Select
						label={__('Type')}
						options={Object.keys(eAccountingi10n.data.taxRateTypes).map(key => {
							return { value: key, label: eAccountingi10n.data.taxRateTypes[key] };
						})}
						value={type}
						before={<Icon icon="bars" />}
						required
						onChange={type => {
							this.setState({ type });
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
