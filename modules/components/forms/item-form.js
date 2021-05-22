/**
 * WordPress dependencies
 */
import { Button } from '@wordpress/components';
/**
 * External dependencies
 */
import Modal from '../modal'
import Form from '../form'
import TextControl from '../text-control'
import PriceControl from '../price-control'
import EntitySelect from '../select-control/entity-select'
import {CategorySelect} from './category-form'
import {createNoticesFromResponse} from '../lib'
import { useEntity } from '@eaccounting/data';
import { __ } from '@wordpress/i18n';
import { isEmpty } from 'lodash';
/**
 * Internal dependencies
 */

export function validateItemForm(values) {
	const errors = {};
	if (isEmpty(values.name)) {
		errors.name = __('Name is required');
	}
	if (isEmpty(values.sale_price)) {
		errors.sale_price = __('Sale price is required');
	}
	if (isEmpty(values.purchase_price)) {
		errors.purchase_price = __('Purchase price is required');
	}
	return errors;
}

export function ItemFormFields(props) {
	const { getInputProps } = props;
	return (
		<>
			<TextControl label={__('Name')} {...getInputProps('name')} />
			<PriceControl
				label={__('Sale price')}
				{...getInputProps('sale_price')}
			/>
			<PriceControl
				label={__('Purchase price')}
				{...getInputProps('purchase_price')}
			/>
			<CategorySelect
				label={__('Category')}
				type={'item'}
				creatable={true}
				{...getInputProps('category')}
			/>
		</>
	);
}

export function ItemForm({ onSave, item = {} }) {
	const { saveEntity, onSaveError } = useEntity({ name: 'items' });
	const onSubmit = async (item) => {
		const res = await saveEntity({ ...item.currency, ...item });
		const {id} = res;
		const error = onSaveError(id);
		createNoticesFromResponse(error);
		if (res && res.id && onSave) {
			onSave(item);
		}
	};
	return (
		<>
			<Form
				initialValues={{
					name: '',
					sale_price: 0,
					purchase_price: 0,
					category: '',
					...item,
				}}
				onSubmitCallback={onSubmit}
				validate={validateItemForm}
			>
				{({ getInputProps, isValidForm, handleSubmit, setValue }) => (
					<>
						<ItemFormFields
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

export function ItemModal({
	onSave,
	item = {},
	onClose,
	title = __('Save Item'),
}) {
	return (
		<>
			<Modal title={title} onClose={onClose}>
				<ItemForm item={item} onSave={onSave} />
			</Modal>
		</>
	);
}

export function ItemSelect({ label, creatable, ...props }){
	return(
		<>
		<EntitySelect
			label={label}
			entity={'items'}
			creatable={creatable}
			modal={<ItemModal title={__('Add new item')}/>}
			{...props}
			/>
		</>
	)
}
