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
import {CurrencySelect} from './currency-form'
import {createNoticesFromResponse} from '../lib'
import EntitySelect from '../select-control/entity-select'

export function validateContactForm(values) {
	const errors = {};
	if (isEmpty(values.name)) {
		errors.name = __('Name is required');
	}
	if (isEmpty(values.currency)) {
		errors.currency = __('Currency is required');
	}
	return errors;
}

export function ContactFormFields(props) {
	const {getInputProps} = props;
	return (
		<>
			<TextControl
				label={__('Name')}
				{...getInputProps('name')} />
			<CurrencySelect
				creatable={true}
				label={__('Currency')}
				{...getInputProps('currency')}
			/>
			<TextControl
				label={__('Company')}
				{...getInputProps('company')} />
			<TextControl
				label={__('Email')}
				type="email"
				{...getInputProps('email')} />
			<TextControl
				label={__('Phone')}
				type="phone"
				{...getInputProps('phone')} />
		</>
	);
}

export function ContactForm({onSave, item = {}, type = 'customers'}) {
	const {saveEntity, onSaveError} = useEntity({
		name: type,
	});
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
					currency: {},
					company: '',
					phone: '',
					email: '',
					...item,
				}}
				onSubmitCallback={onSubmit}
				validate={validateContactForm}
			>
				{({getInputProps, isValidForm, handleSubmit, setValue}) => (
					<>
						<ContactFormFields
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

export function ContactModal({onSave, item = {}, onClose, title = __('Save Contact') }, type='customers') {
	return (
		<>
			<Modal title={title} onClose={onClose}>
				<ContactForm item={item} onSave={onSave} type={type}/>
			</Modal>
		</>
	);
}

/**
 *
 * @param label
 * @param creatable
 * @param type
 * @param props
 * @returns {JSX.Element}
 * @constructor
 */
export function ContactSelect({label, creatable, type = 'customers', ...props }){
	return(
		<>
		<EntitySelect
			label={label}
			entity={type}
			creatable={creatable}
			modal={ <ContactModal title={ __('Add New')} type={type}/>}
			{...props}
			/>
		</>
	)
}

/**
 *
 * @param label
 * @param creatable
 * @param type
 * @param props
 * @returns {JSX.Element}
 * @constructor
 */
export function CustomerSelect({label, creatable, ...props }){
	return(
		<>
			<EntitySelect
				label={label}
				entity={'customers'}
				creatable={creatable}
				modal={ <ContactModal title={ __('Add New Customer')} type={'customers'}/>}
				{...props}
			/>
		</>
	)
}

/**
 *
 * @param label
 * @param creatable
 * @param type
 * @param props
 * @returns {JSX.Element}
 * @constructor
 */
export function VendorSelect({label, creatable, ...props }){
	return(
		<>
			<EntitySelect
				label={label}
				entity={'vendors'}
				creatable={creatable}
				modal={ <ContactModal title={ __('Add New Vendor')} type={'vendors'}/>}
				{...props}
			/>
		</>
	)
}
