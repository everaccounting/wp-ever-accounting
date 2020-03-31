import React, {Component, Fragment} from 'react';
import PropTypes from 'prop-types';
import {__} from '@wordpress/i18n';
import {Form, Modal, TextControl, Select, Icon, Button, SelectControl} from '@eaccounting/components';
import {withSelect} from "@wordpress/data";
import apiFetch from '@wordpress/api-fetch';
import {NotificationManager} from 'react-notifications';


function FormFields(props) {
	const {getInputProps, values, types} = props;
	return (
		<Fragment>
			<TextControl
				label={__('Name')}
				before={<Icon icon="id-card-o"/>}
				required
				{...getInputProps('name')}/>

			<TextControl
				label={__('Rate')}
				before={<Icon icon="money"/>}
				placeholder={__('Enter Rate')}
				required
				help={__('Rate against default currency')}
				{...getInputProps('rate')}/>
				<Select
					label={__('Type')}
					before={<Icon icon="bars" />}
					options={types}
					required
					{...getInputProps('type')}/>
		</Fragment>
	)
}

class EditTaxRate extends Component {
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
		if (!values.type) {
			errors.type = __('Type is required');
		}
		if (!values.rate) {
			errors.rate = __('Rate is required');
		}
		return errors;
	};

	onSubmit = data => {
		let endpoint = '/ea/v1/taxrates';
		if (data.id) {
			endpoint += '/' + data.id;
		}
		console.log(data);
		apiFetch({
			path: endpoint,
			method: 'POST',
			data: {...data, rate: data.rate.toString().replace(/[^\d.]+/g, '')}
		}).then(res => {
			NotificationManager.success(__('Tax rate saved'));
			this.props.onCreate && this.props.onCreate(res);
			this.props.onClose;
		}).catch(error => {
			NotificationManager.error(error.message);
		})
	};

	render() {
		const {tittle = __('Add Currency'), buttonTittle = __('Submit'), onClose} = this.props;
		const {isSaving} = this.state;
		const {types} = this.props;
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
								types={types}
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

export default withSelect(select => {
	return {
		types: select('ea/store/collections').getCollection('taxrates/types')
	}
})(EditTaxRate)
