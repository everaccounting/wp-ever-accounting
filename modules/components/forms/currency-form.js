/**
 * WordPress dependencies
 */
import { Button } from '@wordpress/components';
/**
 * External dependencies
 */
import {
	SelectControl,
	Modal,
	Form,
	TextControl,
	EntitySelect,
} from '@eaccounting/components';
import { useEntity } from '@eaccounting/data';
import { __ } from '@wordpress/i18n';
import { isEmpty } from 'lodash';
/**
 * Internal dependencies
 */
import { ItemModal } from './item';

export function validateCurrencyForm(values) {
	const errors = {};
	if (isEmpty(values.name)) {
		errors.name = __('Name is required');
	}
	if (isEmpty(values.currency)) {
		errors.currency = __('Currency code is required');
	}
	if (isEmpty(values.rate)) {
		errors.rate = __('Currency rate is required');
	}
	return errors;
}

export function CurrencyFormFields(props) {
	const { getInputProps } = props;
	const { entities: currencies } = useEntity({
		name: 'data/currencies',
		query: { perPage: -1 },
	});
	return (
		<>
			<TextControl label={__('Name')} {...getInputProps('name')} />
			<SelectControl
				label={__('Currency Code')}
				getOptionLabel={(option) =>
					option && option.name && `${option.name} (${option.code})`
				}
				getOptionValue={(option) =>
					option && option.code && option.code
				}
				options={currencies}
				{...getInputProps('currency')}
			/>
			<EntitySelect label={__('Currency Code')} entity={'accounts'} />
			<TextControl label={__('Rate')} {...getInputProps('rate')} />
		</>
	);
}

export function CurrencyForm({ onSave, item = {} }) {
	const { saveEntity } = useEntity({ name: 'currencies' });
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
					currency: {},
					rate: '',
					...item,
				}}
				onSubmitCallback={onSubmit}
				validate={validateCurrencyForm}
			>
				{({ getInputProps, isValidForm, handleSubmit, setValue }) => (
					<>
						<CurrencyFormFields
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

export function CurrencyModal({
	onSave,
	item = {},
	onClose,
	title = __('Save Currency'),
}) {
	return (
		<>
			<Modal title={title} onClose={onClose}>
				<CurrencyForm item={item} onSave={onSave} />
			</Modal>
		</>
	);
}

export function CurrencySelect({ label, creatable, ...props }) {
	return (
		<EntitySelect
			label={label}
			entity={'items'}
			creatable={creatable}
			modal={ItemModal}
			{...props}
		/>
	);
}
