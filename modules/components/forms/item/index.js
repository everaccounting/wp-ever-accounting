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
import TextareaControl from '../../textarea-control';

export default function ItemModal( props ) {
	const { item = { id: undefined }, onSave = ( x ) => x, onClose } = props;
	const { title = item.id ? __( 'Update Item' ) : __( 'Add Item' ) } = props;
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
			isSavingEntityRecord: isSavingEntityRecord( 'items', item.id ),
			entityRecordSaveError: getEntityRecordSaveError( 'items', item.id ),
			defaultCurrency: getDefaultCurrency(),
		};
	} );
	const { code = 'USD' } = defaultCurrency;
	const { saveEntityRecord, createNotice } = useDispatch( CORE_STORE_NAME );

	const onSubmit = async ( item ) => {
		const res = await saveEntityRecord( 'items', item );
		if ( ! isSavingEntityRecord && res && res.id ) {
			createNotice( 'success', __( 'Item saved successfully!' ) );
			onSave( res );
		}
	};

	const validate = ( values, errors = {} ) => {
		if ( isEmpty( values.name ) ) {
			errors.name = __( 'Name is required' );
		}
		if ( isEmpty( values.sell_price ) ) {
			errors.sale_price = __( 'Sell price is required' );
		}
		return applyFilters(
			'EACCOUNTING_VALIDATE_ITEM_PARAMS',
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
					} ) => (
						<>
							<TextControl
								required
								label={ __( 'Name' ) }
								{ ...getInputProps( 'name' ) }
							/>
							<TextControl
								required
								before={ code }
								label={ __( 'Sell Price' ) }
								{ ...getInputProps( 'sell_price' ) }
								onChange={ ( val ) =>
									setValue(
										'sell_price',
										val.replace( /[^0-9.]/g, '' )
									)
								}
							/>
							<TextControl
								before={ code }
								label={ __( 'Purchase Price' ) }
								{ ...getInputProps( 'purchase_price' ) }
								onChange={ ( val ) =>
									setValue(
										'purchase_price',
										val.replace( /[^0-9.]/g, '' )
									)
								}
							/>
							<EntitySelect
								required
								label={ __( 'Category' ) }
								type={ 'itemCategories' }
								creatable={ true }
								{ ...getInputProps( 'category' ) }
							/>
							<TabPanel
								tabs={ [
									{
										title: __( 'Details' ),
										name: 'details',
										render: () => {
											return (
												<>
													<TextareaControl
														label={ __(
															'description'
														) }
														{ ...getInputProps(
															'description'
														) }
													/>
												</>
											);
										},
									},
								] }
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
