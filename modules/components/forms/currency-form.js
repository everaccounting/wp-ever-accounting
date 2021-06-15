/**
 * WordPress dependencies
 */
import { Button } from '@wordpress/components';
import apiFetch from '@wordpress/api-fetch';
/**
 * External dependencies
 */
import { useEntity } from '@eaccounting/data';
import { __ } from '@wordpress/i18n';
import {get, isEmpty} from 'lodash';
/**
 * Internal dependencies
 */
import Modal from '../modal'
import Form from '../form'
import TextControl from '../text-control'
import SelectControl from '../select-control'
import EntitySelect from '../select-control/entity-select'
import {createNoticesFromResponse} from '../lib'

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
		name: 'data/currency-codes',
		query: { perPage: -1 },
	});

	return (
		<>
			<TextControl label={__('Name')} {...getInputProps('name')} />
			<SelectControl
				label={__('Currency Code')}
				getOptionLabel={(option) =>
					option && option.name && `${option.name} (${option.code} ${option.symbol} )`
				}
				getOptionValue={(option) =>
					option && option.code && option.code
				}
				options={currencies}
				{...getInputProps('currency')}
			/>
			<TextControl label={__('Rate')} {...getInputProps('rate')} />
		</>
	);
}

export function CurrencyForm({ onSave, item = {} }) {
	const { saveEntity, onSaveError } = useEntity({ name: 'currencies' });
	const onSubmit = async (item) => {
		const res = await saveEntity({ ...item.currency, ...item }, (request) => {
			const path = isEmpty(item.id) ? '/ea/v1/currencies': request.path
			const method = isEmpty(item.id) ? 'POST': request.method
			return apiFetch({...request, path, method});
		});
		const { code } = item;
		const error = onSaveError(code);
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
			entity={'currencies'}
			creatable={creatable}
			renderLabel={(option) => `${option.name} (${option.symbol})`}
			modal={<CurrencyModal title={__('Add new currency')}/>}
			{...props}
		/>
	);
}
