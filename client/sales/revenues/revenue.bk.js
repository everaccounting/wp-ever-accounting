/**
 * WordPress dependencies
 */
import { useSelect } from '@wordpress/data';
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
import { Button, SelectControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import { get, isNumber } from 'lodash';
// eslint-disable-next-line no-unused-vars
export default function Revenue({id, onClose} ) {
	return (
		<Drawer onClose={ onClose }>
			<Drawer.Header className="Header">Payment</Drawer.Header>
			<Drawer.Body>
				<Loading loading={ isRequesting }>
					<Form initialValues={ revenue || {} }>
						{ ( {
							getInputProps,
							isValidForm,
							handleSubmit,
							setValue,
							values,
						} ) => (
							<>
								<TextControl
									label={ __( 'Amount' ) }
									{ ...getInputProps( 'amount' ) }
									before={ get( values, [
										'currency_code',
									] ) }
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
									options={ Object.keys(
										PAYMENT_METHODS
									).map( ( key ) => ( {
										label: PAYMENT_METHODS[ key ],
										value: key,
									} ) ) }
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
							</>
						) }
					</Form>
				</Loading>
			</Drawer.Body>
			<Drawer.Footer>
				<Button isPrimary>Submit</Button>
				<Button isSecondary onClick={ onClose }>
					Cancel
				</Button>
			</Drawer.Footer>
		</Drawer>
	);
}
