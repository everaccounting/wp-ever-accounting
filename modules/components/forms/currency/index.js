/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { Button, SelectControl as Select } from '@wordpress/components';
import { applyFilters } from '@wordpress/hooks';
import apiFetch from '@wordpress/api-fetch';
/**
 * External dependencies
 */
import { isEmpty, trim, get } from 'lodash';
/**
 * Internal dependencies
 */
import Modal from '../../modal';
import Form from '../../form';
import EntitySelect from '../../entity-select';
import TextControl from '../../text-control';
import TabPanel from '../../tab-panel';
import { useDispatch, useSelect } from '@wordpress/data';
import { CORE_STORE_NAME, getSiteData } from '@eaccounting/data';
import { useEffect } from '@wordpress/element';

const currencies = Object.values(getSiteData('codes', []));

export default function CurrencyModal(props) {
	const { item = { code: undefined }, onSave = (x) => x, onClose } = props;
	const { title = item.code ? __('Update Currency') : __('Add Currency') } =
		props;

	const { isSavingEntityRecord, entityRecordSaveError } = useSelect(
		(select) => {
			const { isSavingEntityRecord, getEntityRecordSaveError } =
				select(CORE_STORE_NAME);
			return {
				isSavingEntityRecord: isSavingEntityRecord(
					'currencies',
					item.code
				),
				entityRecordSaveError: getEntityRecordSaveError(
					'currencies',
					item.code
				),
			};
		}
	);

	const { saveEntityRecord, createNotice } = useDispatch(CORE_STORE_NAME);

	const onSubmit = async (item) => {
		const res = await saveEntityRecord(
			'currencies',
			{
				...item,
				code: get(item, ['code', 'code']),
			},
			(request) => {
				const path = isEmpty(item.id)
					? '/ea/v1/currencies'
					: request.path;
				const method = isEmpty(item.id) ? 'POST' : request.method;
				return apiFetch({ ...request, path, method });
			}
		);
		if (!isSavingEntityRecord && res && res.id && onSave) {
			onSave(res);
		}
	};

	const validate = (values, errors = {}) => {
		if (isEmpty(trim(values.name))) {
			errors.name = __('Account Name is required');
		}
		if (isEmpty(values.code)) {
			errors.code = __('Currency code is required');
		}
		if (isEmpty(trim(values.rate))) {
			errors.rate = __('Currency rate is required');
		}
		return applyFilters(
			'eaccounting_validate_currency_params',
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
					initialValues={{ ...item, code: { rate: 1 } }}
					onSubmitCallback={onSubmit}
					validate={validate}
				>
					{({
						getInputProps,
						isValidForm,
						handleSubmit,
						setValue,
					}) => (
						<>
							<EntitySelect
								label={__('Currency Code')}
								getOptionLabel={(option) =>
									option &&
									option.name &&
									`${option.name} (${option.code} ${option.symbol} )`
								}
								getOptionValue={(option) =>
									option && option.code && option.code
								}
								options={currencies}
								{...getInputProps('code')}
								onChange={(code) => {
									setValue('symbol', code.symbol);
									setValue('name', code.name);
									setValue('rate', code.rate);
									setValue('position', code.position);
									setValue(
										'decimal_separator',
										code.decimal_separator
									);
									setValue(
										'thousand_separator',
										code.thousand_separator
									);
									setValue('code', code);
								}}
							/>
							<TextControl
								label={__('Name')}
								{...getInputProps('name')}
							/>
							<TextControl
								label={__('Rate')}
								{...getInputProps('rate')}
								onChange={(val) =>
									setValue(
										'rate',
										val.replace(/[^0-9.]/g, '')
									)
								}
							/>
							<TabPanel
								tabs={applyFilters(
									'eaccounting_currency_modal_tabs',
									[
										{
											title: __('Details'),
											name: 'details',
											render: () => {
												return (
													<>
														<TextControl
															label={__('Symbol')}
															{...getInputProps(
																'symbol'
															)}
														/>
														<Select
															label={__(
																'Position'
															)}
															options={[
																{
																	label: __(
																		'Before'
																	),
																	value: 'before',
																},
																{
																	label: __(
																		'After'
																	),
																	value: 'after',
																},
															]}
															{...getInputProps(
																'position'
															)}
														/>
														<TextControl
															label={__(
																'Decimal Separator'
															)}
															{...getInputProps(
																'decimal_separator'
															)}
														/>
														<TextControl
															label={__(
																'Thousand Separator'
															)}
															{...getInputProps(
																'thousand_separator'
															)}
														/>
													</>
												);
											},
										},
									],
									getInputProps
								)}
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
