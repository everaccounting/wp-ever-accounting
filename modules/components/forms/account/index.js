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
import { isEmpty, trim, get } from 'lodash';
/**
 * Internal dependencies
 */
import Modal from '../../modal';
import Form from '../../form';
import TextControl from '../../text-control';
import EntitySelect from '../../entity-select';
import TabPanel from '../../tab-panel';
import { CORE_STORE_NAME } from '@eaccounting/data';
import TextareaControl from '../../textarea-control';

export default function AccountModal( props ) {
	const { item = { id: undefined }, onSave = ( x ) => x, onClose } = props;
	const {
		title = item.id ? __( 'Update Account' ) : __( 'Add Account' ),
	} = props;
	const {
		isSavingEntityRecord,
		entityRecordSaveError,
		defaultCurrency,
	} = useSelect( ( select ) => {
		const {
			isSavingEntityRecord,
			getEntityRecordSaveError,
			getDefaultCurrency,
		} = select( CORE_STORE_NAME );
		return {
			isSavingEntityRecord: isSavingEntityRecord( 'accounts', item.id ),
			entityRecordSaveError: getEntityRecordSaveError(
				'accounts',
				item.id
			),
			defaultCurrency: getDefaultCurrency(),
		};
	} );

	const { saveEntityRecord, createNotice } = useDispatch( CORE_STORE_NAME );

	const onSubmit = async ( item ) => {
		const res = await saveEntityRecord( 'accounts', item );
		if ( ! isSavingEntityRecord && res && res.id ) {
			createNotice( 'success', __( 'Account saved successfully!' ) );
			onSave( res );
		}
	};

	const validate = ( values, errors = {} ) => {
		if ( isEmpty( trim( values.name ) ) ) {
			errors.name = __( 'Account Name is required' );
		}
		if ( isEmpty( values.number ) ) {
			errors.number = __( 'Account number is required' );
		}
		if ( isEmpty( trim( values.currency ) ) ) {
			errors.currency = __( 'Currency is required' );
		}
		return applyFilters(
			'EACCOUNTING_VALIDATE_CURRENCY_PARAMS',
			errors,
			values
		);
	};

	useEffect( () => {
		// eslint-disable-next-line no-unused-expressions
		entityRecordSaveError &&
			createNotice( 'error', entityRecordSaveError.message );
	}, [ entityRecordSaveError ] );

	return (
		<>
			<Modal title={ title } onClose={ onClose }>
				<Form
					initialValues={ {
						opening_balance: '0.00',
						currency: defaultCurrency,
						...item,
					} }
					onSubmitCallback={ onSubmit }
					validate={ validate }
				>
					{ ( {
						getInputProps,
						isValidForm,
						handleSubmit,
						setValue,
						values,
					} ) => (
						<>
							<TextControl
								required
								label={ __( 'Name' ) }
								{ ...getInputProps( 'name' ) }
							/>
							<TextControl
								required
								label={ __( 'Account Number' ) }
								{ ...getInputProps( 'number' ) }
							/>
							<EntitySelect
								required
								label={ __( 'Account Currency' ) }
								entityName={ 'currencies' }
								creatable={ true }
								{ ...getInputProps( 'currency' ) }
							/>
							<TextControl
								label={ __( 'Opening Balance' ) }
								{ ...getInputProps( 'opening_balance' ) }
								before={ get( values, [ 'currency', 'code' ] ) }
								onChange={ ( val ) =>
									setValue(
										'opening_balance',
										val.replace( /[^0-9.]/g, '' )
									)
								}
							/>
							<TabPanel
								tabs={ applyFilters(
									'EACCOUNTING_ACCOUNT_TABS_PANELS',
									[
										{
											title: __( 'Details' ),
											name: 'details',
											render: () => {
												return (
													<>
														<TextControl
															label={ __(
																'Bank Name'
															) }
															{ ...getInputProps(
																'bank_name'
															) }
														/>
														<TextControl
															label={ __(
																'Bank Phone'
															) }
															{ ...getInputProps(
																'bank_phone'
															) }
														/>
														<TextareaControl
															label={ __(
																'Bank Address'
															) }
															{ ...getInputProps(
																'bank_address'
															) }
														/>
													</>
												);
											},
										},
									],
									getInputProps
								) }
							/>
							<Button
								type="submit"
								isPrimary
								disabled={
									! isValidForm || isSavingEntityRecord
								}
								onClick={ handleSubmit }
							>
								{ __( 'Submit' ) }
							</Button>
						</>
					) }
				</Form>
			</Modal>
		</>
	);
}
