import React, {Component, Fragment} from 'react';
import PropTypes from 'prop-types';
import {__} from '@wordpress/i18n';
import {Form, Modal, TextControl, Select, Icon, Button} from '@eaccounting/components';
import {ColorPicker, Popover} from '@wordpress/components';
import "./style.scss";
import {withSelect} from "@wordpress/data";
import apiFetch from '@wordpress/api-fetch';
import {NotificationManager} from 'react-notifications';

class EditCategory extends Component {
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
			colorPickerOpen: false,
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
		return errors;
	};

	onSubmit = data => {
		let endpoint = '/ea/v1/categories';
		if (data.id) {
			endpoint += '/' + data.id;
		}

		apiFetch({
			path: endpoint,
			method: 'POST',
			data
		}).then(res => {
			NotificationManager.success(__('Category saved'));
			this.props.onCreate && this.props.onCreate(res);
			this.props.onClose;
		}).catch(error => {
			NotificationManager.error(error.message);
		})
	};


	render() {
		const {tittle = __('Add Category'), buttonTittle = __('Submit'), onClose} = this.props;
		const {type = 'income'} = this.props.item;
		const {colorPickerOpen, isSaving} = this.state;

		return (
			<Modal title={tittle} onRequestClose={onClose}>
				<Form
					validate={this.validate}
					onSubmitCallback={this.onSubmit}
					initialValues={this.props.item}>
					{({getInputProps, values, errors, handleSubmit}) => (
						<Fragment>
							<TextControl
								label={__('Category Name')}
								before={<Icon icon="id-card-o"/>}
								required
								{...getInputProps('name')}
							/>

							<Select
								label={__('Category Type')}
								before={<Icon icon="bars"/>}
								required
								options={this.props.types}
								{...getInputProps('type')}/>


							<TextControl
								label={__('Color')}
								before={<span className="ea-color-preview" style={{backgroundColor: values.color}}/>}
								className={'ea-color-picker'}
								onClick={() => {
									this.setState({colorPickerOpen: !this.state.colorPickerOpen});
								}}
								{...getInputProps('color')}/>

							{colorPickerOpen && (
								<Popover poistion="middle-right" className="ea-modal-color-picker"
										 style={{position: 'initial'}}>
									<ColorPicker color={values.color || ''} onChangeComplete={(color) => {
										values.color = color.hex;
										this.setState({colorPickerOpen: false})
									}} disableAlpha/>
								</Popover>
							)}

							<Button isPrimary isBusy={isSaving} onClick={handleSubmit}
									disabled={Object.keys(errors).length}>
								{buttonTittle}
							</Button>

						</Fragment>
					)}
				</Form>
			</Modal>
		);
	}
}

export default withSelect(select => {
	return {
		types: select('ea/store/collections').getCollection('categories/types')
	}
})(EditCategory)
