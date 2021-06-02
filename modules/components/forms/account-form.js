/**
 * WordPress dependencies
 */
import {Button} from '@wordpress/components';
/**
 * External dependencies
 */
import {useEntity, useSettings} from '@eaccounting/data';
import {__} from '@wordpress/i18n';
import {isEmpty} from 'lodash';

import Modal from '../modal'
import Form from '../form'
import TextControl from '../text-control'
import {createNoticesFromResponse} from '../lib'
import EntitySelect from '../select-control/entity-select'
import {CurrencySelect} from './currency-form';
import PriceControl from "../price-control";

/**
 * Validate account form.
 *
 * @param values
 * @returns {{}}
 */
export function validateAccountForm(values) {
	const errors = {};
	if (isEmpty(values.name)) {
		errors.name = __('Name is required');
	}
	if (isEmpty(values.number)) {
		errors.number = __('Account number is required');
	}
	if (isEmpty(values.currency)) {
		errors.currency = __('Account currency is required');
	}
	return errors;
}

/**
 * Account form fields.
 *
 * @param props
 * @returns {JSX.Element}
 * @constructor
 */
export function AccountFormFields(props) {
	const {getInputProps, values} = props;
	return (
		<>
			<TextControl label={__('Name')} {...getInputProps('name')} />
			<TextControl label={__('Account Number')} {...getInputProps('number')} />
			<CurrencySelect label={__('Account currency')} creatable={true} {...getInputProps('currency')} />
			<PriceControl label={__('Opening Balance')} code={values.currency.code} {...getInputProps('opening_balance')} />
		</>
	);
}

/**
 * Account form.
 *
 * @param onSave
 * @param item
 * @returns {JSX.Element}
 * @constructor
 */
export function AccountForm({onSave, item = {}}) {
	const {saveEntity, onSaveError} = useEntity({
		name: 'accounts',
	});
	const {defaultCurrency} = useSettings();
	const onSubmit = async (item) => {
		const res = await saveEntity({...item});
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
					number: '',
					currency: defaultCurrency,
					opening_balance: 0,
					...item,
				}}
				onSubmitCallback={onSubmit}
				validate={validateAccountForm}
			>
				{({getInputProps, isValidForm, handleSubmit, values, setValue}) => (
					<>
						<AccountFormFields
							getInputProps={getInputProps}
							setValue={setValue}
							values={values}
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

/**
 * Account modal.
 *
 * @param onSave
 * @param item
 * @param onClose
 * @param title
 * @returns {JSX.Element}
 * @constructor
 */
export function AccountModal({onSave, item = {}, onClose, title = __('Save Account')}) {
	return (
		<>
			<Modal title={title} onClose={onClose}>
				<AccountForm item={item} onSave={onSave}/>
			</Modal>
		</>
	);
}

/**
 * Account select.
 *
 * @param label
 * @param creatable
 * @param props
 * @returns {JSX.Element}
 * @constructor
 */
export function AccountSelect({ label, creatable, ...props }) {
	return (
		<>
			<EntitySelect
				label={label}
				entity={'accounts'}
				creatable={creatable}
				renderLabel={(option) => `${option.name} (${option.currency.symbol})`}
				modal={<AccountModal title={ __('Add Account')}/>}
				{...props}
			/>
		</>
	);
}
