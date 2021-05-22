/**
 * WordPress dependencies
 */
import {Button} from '@wordpress/components';
/**
 * External dependencies
 */
import {useEntity} from '@eaccounting/data';
import {__} from '@wordpress/i18n';
import {isEmpty} from 'lodash';

import Modal from '../modal'
import Form from '../form'
import TextControl from '../text-control'
import {createNoticesFromResponse} from '../lib'
import EntitySelect from '../select-control/entity-select'

export function validateCategoryForm(values) {
	const errors = {};
	if (isEmpty(values.name)) {
		errors.name = __('Name is required');
	}
	if (isEmpty(values.type)) {
		errors.type = __('Category type is required');
	}
	return errors;
}

export function CategoryFormFields(props) {
	const {getInputProps} = props;
	return (
		<>
			<TextControl label={__('Name')} {...getInputProps('name')} />
		</>
	);
}

export function CategoryForm({onSave, item = {}}) {
	const {saveEntity, onSaveError} = useEntity({
		name: 'categories',
	});
	const onSubmit = async (item) => {
		const res = await saveEntity({...item.currency, ...item});
		const {id} = item;
		const error = onSaveError(id);
		createNoticesFromResponse(error);
		if (!error && res && res.id && onSave) {
			onSave(item);
		}
	};
	return (
		<>
			<Form
				initialValues={{
					name: '',
					type: '',
					...item,
				}}
				onSubmitCallback={onSubmit}
				validate={validateCategoryForm}
			>
				{({getInputProps, isValidForm, handleSubmit, setValue}) => (
					<>
						<CategoryFormFields
							getInputProps={getInputProps}
							setValue={setValue}
						/>
						<Button
							type="submit"
							isPrimary
							disabled={!isValidForm}
							onClick={handleSubmit}
						>
							{__('Submit')}
						</Button>
					</>
				)}
			</Form>
		</>
	);
}

export function CategoryModal({onSave, item = {}, onClose, title = __('Save Category'),type ='income'}) {
	return (
		<>
			<Modal title={title} onClose={onClose}>
				<CategoryForm item={{...item, type}} onSave={onSave}/>
			</Modal>
		</>
	);
}

export function CategorySelect({ label, creatable, type = 'income', ...props }) {
	return (
		<>
		<EntitySelect
			label={label}
			entity={'categories'}
			creatable={creatable}
			modal={<CategoryModal title={ __('Add Category')} type={type} />}
			query={{type:type}}
			{...props}
			/>
		</>
	);
}
