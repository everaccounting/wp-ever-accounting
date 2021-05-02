/**
 * WordPress dependencies
 */
import { Button } from '@wordpress/components';
/**
 * External dependencies
 */
import {
	Modal,
	Form,
	TextControl,
	EntitySelect,
	PriceControl,
} from '@eaccounting/components';
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
			<EntitySelect
				label={__('Category')}
				entity={'categories'}
				query={{ type: 'item' }}
				{...getInputProps('category')}
			/>
		</>
	);
}

export function ItemForm({ onSave, item = {} }) {
	const { saveEntity } = useEntity({ name: 'items' });
	const onSubmit = async (item) => {
		const res = await saveEntity({ ...item.currency, ...item });
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
