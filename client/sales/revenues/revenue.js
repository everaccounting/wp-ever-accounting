/**
 * WordPress dependencies
 */
import { withDispatch, withSelect } from '@wordpress/data';
import { Button, ButtonGroup, SelectControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
/**
 * External dependencies
 */
import { get } from 'lodash';
import { compose } from '@wordpress/compose';
/**
 * External dependencies
 */
import { PAYMENT_METHODS } from '@eaccounting/data';
import {
	Drawer,
	Form,
	DatePicker,
	TextControl,
	Loading,
	EntitySelect,
	TextareaControl,
} from '@eaccounting/components';

const entityName = 'revenues';

// eslint-disable-next-line no-unused-vars
function Revenue( props ) {
	const {
		item,
		payment_id,
		isRequesting,
		onClose,
		isSavingEntityRecord,
		saveEntityRecord,
	} = props;

	const onSubmit = async ( form ) => {
		const res = await saveEntityRecord( form );
		console.log( res );
	};

	return (
		<Form initialValues={ { ...item } } onSubmitCallback={ onSubmit }>
			{ ( {
				getInputProps,
				isValidForm,
				handleSubmit,
				setValue,
				values,
			} ) => (
				<Drawer onClickOutSide={ onClose } title={ __( 'Revenue' ) }>
					<div className="eaccounting-drawer__body">
						<Loading
							loading={ isRequesting }
							text={ __( 'Loading' ) }
						>
							<TextControl
								label={ __( 'Amount' ) }
								{ ...getInputProps( 'amount' ) }
								before={ get( values, [ 'currency_code' ] ) }
								onChange={ ( val ) =>
									setValue(
										'amount',
										val.replace( /[^0-9.]/g, '' )
									)
								}
							/>
							<DatePicker
								label={ __( 'Payment Date' ) }
								date={
									values &&
									values.birth_date &&
									values.birth_date
								}
								{ ...getInputProps( 'payment_date' ) }
							/>
							<EntitySelect
								label={ __( 'Account' ) }
								entityName="accounts"
								{ ...getInputProps( 'account' ) }
							/>
							<EntitySelect
								label={ __( 'Category' ) }
								entityName="incomeCategories"
								{ ...getInputProps( 'category' ) }
							/>
							<EntitySelect
								label={ __( 'Customer' ) }
								entityName="customers"
								{ ...getInputProps( 'customer' ) }
							/>
							<SelectControl
								label={ __( 'Payment Method' ) }
								options={ Object.keys( PAYMENT_METHODS ).map(
									( key ) => ( {
										label: PAYMENT_METHODS[ key ],
										value: key,
									} )
								) }
								{ ...getInputProps( 'payment_method' ) }
							/>
							<TextControl
								label={ __( 'Reference' ) }
								{ ...getInputProps( 'reference' ) }
							/>
							<TextareaControl
								label={ __( 'Description' ) }
								{ ...getInputProps( 'description' ) }
							/>
						</Loading>
					</div>
					<div className="eaccounting-drawer__footer">
						<ButtonGroup>
							<Button
								isPrimary
								disabled={
									isRequesting ||
									! isValidForm ||
									isSavingEntityRecord( payment_id )
								}
								onClick={ handleSubmit }
							>
								{ __( 'Submit' ) }
							</Button>
							<Button isSecondary onClick={ onClose }>
								{ __( 'Cancel' ) }
							</Button>
						</ButtonGroup>
					</div>
				</Drawer>
			) }
		</Form>
	);
}

const applyWithSelect = withSelect( ( select, props ) => {
	const { payment_id = null } = props;

	const {
		getEntityRecord,
		isResolving,
		getDefaultCurrency,
		isSavingEntityRecord,
	} = select( 'ea/core' );
	return {
		item: getEntityRecord( entityName, payment_id ),
		isRequesting: isResolving( 'getEntityRecord', [
			entityName,
			payment_id,
		] ),
		isSavingEntityRecord: isSavingEntityRecord( entityName ),
		defaultCurrency: getDefaultCurrency(),
		payment_id,
	};
} );

const applyWithDispatch = withDispatch( ( dispatch ) => {
	const { deleteEntityRecord, saveEntityRecord } = dispatch( 'ea/core' );
	return {
		deleteEntityRecord: ( id ) => deleteEntityRecord( entityName, id ),
		saveEntityRecord: ( item ) => saveEntityRecord( entityName, item ),
	};
} );

export default compose( [ applyWithSelect, applyWithDispatch ] )( Revenue );
