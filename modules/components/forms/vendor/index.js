/**
 * WordPress dependencies
 */
import { Button } from '@wordpress/components';
import { useSelect, useDispatch } from '@wordpress/data';
import { __ } from '@wordpress/i18n';
import { useEffect } from '@wordpress/element';
import { applyFilters } from '@wordpress/hooks';
/**
 * External dependencies
 */
import { isEmpty } from 'lodash';
/**
 * Internal dependencies
 */
import Modal from '../../modal';
import Form from '../../form';
import TextControl from '../../text-control';
import EntitySelect from '../../entity-select';
import TabPanel from '../../tab-panel';
import { CORE_STORE_NAME } from '@eaccounting/data';
import DatePicker from '../../date-picker';

export default function VendorModal(props) {
	const { item = { id: undefined }, onSave = (x) => x, onClose } = props;
	const { title = item.id ? __('Update Vendor') : __('Add Vendor') } = props;
	const { isSavingEntityRecord, entityRecordSaveError, defaultCurrency } =
		useSelect((select) => {
			const {
				isSavingEntityRecord,
				getEntityRecordSaveError,
				getDefaultCurrency,
			} = select(CORE_STORE_NAME);
			return {
				isSavingEntityRecord: isSavingEntityRecord('vendors', item.id),
				entityRecordSaveError: getEntityRecordSaveError(
					'vendors',
					item.id
				),
				defaultCurrency: getDefaultCurrency(),
			};
		});

	const { saveEntityRecord, createNotice } = useDispatch(CORE_STORE_NAME);

	const onSubmit = async (item) => {
		const res = await saveEntityRecord('vendors', item);
		if (!isSavingEntityRecord && res && res.id) {
			createNotice('success', __('Vendor saved successfully!'));
			onSave(res);
		}
	};

	const validate = (values, errors = {}) => {
		if (isEmpty(values.name)) {
			errors.name = __('Name is required');
		}
		if (isEmpty(values.currency)) {
			errors.currency = __('Currency is required');
		}
		return applyFilters(
			'EACCOUNTING_VALIDATE_VENDOR_PARAMS',
			errors,
			values
		);
	};

	useEffect(() => {
		// eslint-disable-next-line no-unused-expressions
		entityRecordSaveError &&
			createNotice('error', entityRecordSaveError.message);
	}, [entityRecordSaveError]);

	return (
		<>
			<Modal title={title} onClose={onClose}>
				<Form
					initialValues={{
						currency: defaultCurrency,
						...item,
					}}
					onSubmitCallback={onSubmit}
					validate={validate}
				>
					{({ getInputProps, isValidForm, handleSubmit }) => (
						<>
							<TextControl
								label={__('Name')}
								{...getInputProps('name')}
							/>
							<EntitySelect
								required
								label={__('Currency')}
								type={'currencies'}
								creatable={true}
								{...getInputProps('currency')}
							/>
							<TabPanel
								tabs={[
									{
										title: __('Details'),
										name: 'details',
										render: () => {
											return (
												<>
													<TextControl
														label={__('Email')}
														type="email"
														{...getInputProps(
															'email'
														)}
													/>
													<TextControl
														label={__('Phone')}
														{...getInputProps(
															'phone'
														)}
													/>
													<DatePicker
														label={__('Birth Date')}
														{...getInputProps(
															'birth_date'
														)}
													/>
													<TextControl
														label={__('Website')}
														{...getInputProps(
															'website'
														)}
														validate={(val) =>
															val.replace(
																/[^0-9.]/g,
																''
															)
														}
													/>
													<TextControl
														label={__(
															'Company Name'
														)}
														{...getInputProps(
															'company'
														)}
													/>
													<TextControl
														label={__('Vat')}
														{...getInputProps(
															'vat_number'
														)}
													/>
												</>
											);
										},
									},
									{
										title: __('Contact Details'),
										name: 'contact-details',
										render: () => {
											return (
												<>
													<TextControl
														label={__('Street')}
														{...getInputProps(
															'street'
														)}
													/>
													<TextControl
														label={__('City')}
														{...getInputProps(
															'city'
														)}
													/>
													<TextControl
														label={__('State')}
														{...getInputProps(
															'state'
														)}
													/>
													<TextControl
														label={__('Post Code')}
														{...getInputProps(
															'post_code'
														)}
													/>
													<EntitySelect
														label={__('Country')}
														type="countries"
														{...getInputProps(
															'country'
														)}
													/>
												</>
											);
										},
									},
								]}
							/>
							<Button
								type="submit"
								isPrimary
								disabled={!isValidForm || isSavingEntityRecord}
								onClick={handleSubmit}
							>
								{__('Submit')}
							</Button>
						</>
					)}
				</Form>
			</Modal>
		</>
	);
}
