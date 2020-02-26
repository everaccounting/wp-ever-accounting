import React, {Component, Fragment} from 'react';
import PropTypes from 'prop-types';
import {translate as __} from 'lib/locale';
import {connect} from 'react-redux';
import notify from "lib/notify";
import {
	Modal,
	TextControl,
	ReactSelect,
	ToggleControl,
	Form,
	Icon,
	Button
} from '@eaccounting/components';
import {includes} from 'lodash';
import {createCategory, updateCategory} from 'state/categories/action'
import {initialCategory} from 'state/categories/selection';

const Types = [
	{
		label: __('Expense'),
		value: 'expense',
	},
	{
		label: __('Income'),
		value: 'income',
	},
	{
		label: __('Item'),
		value: 'item',
	},
	{
		label: __('Other'),
		value: 'other',
	}
];


class EditCategory extends Component {
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
		const {name, type, enabled = true} = props.item;
		this.state = {
			name,
			enabled,
			isSaving: false,
		};
	}

	validate = values => {
		const errors = {};
		console.log(values);
		console.log(values.type);
		if (!values.name) {
			errors.name = __('Name is required');
		}

		if (('object' !== typeof values.type) || !values.type.length) {
			errors.type = __('Type is required');
		}
		return errors;
	};

	onSubmit = data => {
		const item = {...data, type: data.type.value, status: data.enabled ? 'active' : 'inactive', enabled: undefined};
		const {id = null} = this.props.item;
		if (id) {
			this.props.onSave(id, item);
		} else {
			this.props.onCreate(item);
		}

		this.props.onClose ? this.props.onClose(data) : () => {
		};

	};

	render() {
		const {tittle = __('Add Category'), buttonTittle = __('Submit'), onClose} = this.props;
		const {isSaving} = this.state;
		const {name, type, enabled = true} = this.props.item;
		const categoryType = Types.filter((filter, index) => {
			return includes(type, filter.value) === true;
		});
		const props = {
			name,
			type: categoryType,
			enabled
		};

		return (
			<Modal title={tittle} onRequestClose={onClose}>
				<Form validate={this.validate} onSubmitCallback={this.onSubmit} initialValues={props}>
					{({getInputProps, values, errors, handleSubmit}) => (
						<Fragment>

							<TextControl label={__('Category Name')}
										 before={<Icon icon='id-card-o'/>}
										 required
										 {...getInputProps('name')}/>

							<ReactSelect label={__('Category Type')}
										 before={<Icon icon='bars'/>}
										 options={Types}
										 required
										 {...getInputProps('type')}/>
							<ToggleControl
								label={__('Enabled')}
								{...getInputProps('enabled')}/>
							<Button isPrimary
									isBusy={isSaving}
									onClick={handleSubmit}
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

function mapDispatchToProps(dispatch) {
	return {
		onSave: (id, item) => {
			dispatch(updateCategory(id, item));
		},
		onCreate: item => {
			dispatch(createCategory(item));
		}
	};
}

export default connect(
	null,
	mapDispatchToProps,
)(EditCategory);

