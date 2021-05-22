/**
 * WordPress dependencies
 */
import { Button } from '@wordpress/components';
/**
 * External dependencies
 */
import { useEntity } from '@eaccounting/data';
import { __ } from '@wordpress/i18n';
import { isEmpty } from 'lodash';

import Modal from '../modal'
import Form from '../form'
import TextControl from '../text-control'
import {createNoticesFromResponse} from '../lib'

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
	const { getInputProps } = props;
	return (
		<>
			<TextControl label={__('Name')} {...getInputProps('name')} />
		</>
	);
}

export function CategoryForm({ onSave, item = {} }) {
	const { saveEntity, onSaveError } = useEntity({
		name: 'categories',
	});
	const onSubmit = async (item) => {
		const res = await saveEntity({ ...item.currency, ...item });
		const { id } = item;
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
					currency: {},
					rate: '',
					...item,
				}}
				onSubmitCallback={onSubmit}
				validate={validateCategoryForm}
			>
				{({ getInputProps, isValidForm, handleSubmit, setValue }) => (
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

export function CategoryModal({
								  onSave,
								  item = {},
								  onClose,
								  title = __('Save Category'),
							  }) {
	return (
		<>
			<Modal title={title} onClose={onClose}>
				<CategoryForm item={item} onSave={onSave} />
			</Modal>
		</>
	);
}

export function IncomeCategoryModal({
										onSave,
										item = {},
										onClose,
										title = __('Save Category'),
									}) {
	return (
		<>
			<Modal title={title} onClose={onClose}>
				<CategoryForm
					item={{ ...item, type: 'income' }}
					onSave={onSave}
				/>
			</Modal>
		</>
	);
}

export function ExpenseCategoryModal({
										 onSave,
										 item = {},
										 onClose,
										 title = __('Save Category'),
									 }) {
	return (
		<>
			<Modal title={title} onClose={onClose}>
				<CategoryForm
					item={{ ...item, type: 'expense' }}
					onSave={onSave}
				/>
			</Modal>
		</>
	);
}

export function ItemCategoryModal({
									  onSave,
									  item = {},
									  onClose,
									  title = __('Save Category'),
								  }) {
	return (
		<>
			<Modal title={title} onClose={onClose}>
				<CategoryForm
					item={{ ...item, type: 'item' }}
					onSave={onSave}
				/>
			</Modal>
		</>
	);
}

export function CategorySelect() {
	return <>Category Select</>;
}
