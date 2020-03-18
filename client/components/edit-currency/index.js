import React, {Component, Fragment} from 'react';
import PropTypes from 'prop-types';
import {__} from '@wordpress/i18n';
import {Form, Modal, TextControl, Select, Icon, Button, SelectControl} from '@eaccounting/components';
import {withSelect} from "@wordpress/data";
import apiFetch from '@wordpress/api-fetch';
import {NotificationManager} from 'react-notifications';

function FormFields(props) {
	const {getInputProps, values} = props;
	return (
		<Fragment>
			<TextControl
				label={__('Name')}
				before={<Icon icon="id-card-o"/>}
				required
				{...getInputProps('name')}/>

			<SelectControl
				label={__('Code')}
				getOptionLabel={option => option && option.currency && option.currency}
				getOptionValue={option => option && option.currency && option.currency}
				options={Object.values(eAccountingi10n.data.currencies)}
				before={<Icon icon="code"/>}
				required
				{...getInputProps('code')}
				value={eAccountingi10n.data.currencies[values.code]}/>

			<TextControl
				label={__('Rate')}
				before={<Icon icon="money"/>}
				placeholder={__('Enter Rate')}
				required
				help={__('Rate against default currency')}
				{...getInputProps('rate')}/>
		</Fragment>
	)
}

export default class EditCurrency extends Component {
	static propTypes = {
		item: PropTypes.object,
		onClose: PropTypes.func,
		onCreate: PropTypes.func,
		tittle: PropTypes.string,
		buttonTittle: PropTypes.string,
	};

	static defaultProps = {
		item: {},
	};


	constructor(props) {
		super(props);
		this.state = {
			isSaving: false,
		};
	}

	validate = (values) => {
		const errors = {};
		if (!values.name) {
			errors.name = __('Name is required');
		}
		if (!values.code) {
			errors.code = __('Code is required');
		}
		if (!values.rate) {
			errors.rate = __('Rate is required');
		}
		return errors;
	};

	onSubmit = data => {
		let endpoint = '/ea/v1/currencies';
		if (data.id) {
			endpoint += '/' + data.id;
		}
		console.log(data);
		apiFetch({
			path: endpoint,
			method: 'POST',
			data:{...data, code:data.code.currency, rate: data.rate.toString().replace(/[^\d.]+/g, '')}
		}).then(res => {
			NotificationManager.success(__('Currency saved'));
			this.props.onCreate && this.props.onCreate(res);
			this.props.onClose;
		}).catch(error => {
			NotificationManager.error(error.message);
		})
	};

	render() {
		const {tittle = __('Add Currency'), buttonTittle = __('Submit'), onClose} = this.props;
		const {isSaving} = this.state;

		return (
			<Modal title={tittle} onRequestClose={onClose}>
				<Form
					validate={this.validate}
					onSubmitCallback={this.onSubmit}
					initialValues={this.props.item}>
					{({getInputProps, values, errors, handleSubmit}) => (
						<Fragment>
							<FormFields
								values={values}
								getInputProps={getInputProps}
								handleSubmit={handleSubmit}
								errors={errors}/>
							<Button isPrimary isBusy={isSaving} onClick={handleSubmit}
									disabled={Object.keys(errors).length}>
								{buttonTittle}
							</Button>
						</Fragment>
					)}
				</Form>
			</Modal>
		)
	}
}
