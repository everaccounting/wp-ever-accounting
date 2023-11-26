/**
 * External dependencies
 */
import { Form, Button, Text, Space } from '@eac/components';
import { useSettings } from '@eac/data';
/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
function Company() {
	const settings = useSettings();

	return (
		<Form
			enableReinitialize
			initialValues={ {
				...settings?.options,
			} }
			validations={ {
				company_name: Form.is.required(),
				company_email: [ Form.is.required(), Form.is.email() ],
			} }
			onSubmit={ ( values ) => {
				return settings.updateOptions( values );
			} }
		>
			{ ( { dirty, isSubmitting, isValid, handleSubmit } ) => (
				<>
					<Text as="h3" size="14" lineHeight="1.75">
						{ __( 'Company Details', 'wp-ever-accounting' ) }
					</Text>
					<Text as="p" style={ { marginBottom: '20px' } } color="gray">
						{ __(
							'Enter your company details. This information will be used in your invoices and other documents.',
							'wp-ever-accounting'
						) }
					</Text>
					<Space size="medium" direction="vertical" style={ { display: 'flex' } }>
						<Form.Field.Input
							name="company_name"
							label={ __( 'Company Name', 'wp-ever-accounting' ) }
							placeholder={ __( 'e.g. XYZ Company', 'wp-ever-accounting' ) }
						/>
						<Form.Field.Input
							name="company_email"
							label={ __( 'Company Email', 'wp-ever-accounting' ) }
							placeholder={ __( 'john@xyz.com', 'wp-ever-accounting' ) }
						/>
						<Form.Field.Input
							name="company_phone"
							label={ __( 'Company Phone', 'wp-ever-accounting' ) }
							placeholder={ __( '+123456789', 'wp-ever-accounting' ) }
						/>
						<Form.Field.Input
							name="company_vat"
							label={ __( 'Company VAT', 'wp-ever-accounting' ) }
							placeholder={ __( 'Enter company VAT', 'wp-ever-accounting' ) }
						/>
					</Space>

					<Text as="p" size="14" lineHeight="1.75" style={ { marginTop: '20px' } }>
						{ __( 'Company Address', 'wp-ever-accounting' ) }
					</Text>
					<Text as="p" style={ { marginBottom: '20px' } } color="gray">
						{ __(
							'Business address details. The address will be used in the invoices, bills, and other records that you issue..',
							'wp-ever-accounting'
						) }
					</Text>
					<Space size="medium" direction="vertical" style={ { display: 'flex' } }>
						<Form.Field.Input
							name="company_address_1"
							label={ __( 'Address Line 1', 'wp-ever-accounting' ) }
							placeholder={ __( 'Enter address line 1', 'wp-ever-accounting' ) }
						/>
						<Form.Field.Input
							name="company_address_2"
							label={ __( 'Address Line 2', 'wp-ever-accounting' ) }
							placeholder={ __( 'Enter address line 2', 'wp-ever-accounting' ) }
						/>
						<Form.Field.Input
							name="company_city"
							label={ __( 'City', 'wp-ever-accounting' ) }
							placeholder={ __( 'Enter city', 'wp-ever-accounting' ) }
						/>
						<Form.Field.Input
							name="company_state"
							label={ __( 'State', 'wp-ever-accounting' ) }
							placeholder={ __( 'Enter state', 'wp-ever-accounting' ) }
						/>
						<Form.Field.Input
							name="company_zip"
							label={ __( 'Zip', 'wp-ever-accounting' ) }
							placeholder={ __( 'Enter zip', 'wp-ever-accounting' ) }
						/>
						<Form.Field.Input
							name="company_country"
							label={ __( 'Country', 'wp-ever-accounting' ) }
							placeholder={ __( 'Enter country', 'wp-ever-accounting' ) }
						/>
						<Button
							onClick={ handleSubmit }
							disabled={ ! dirty || isSubmitting || ! isValid }
							isPrimary
						>
							{ __( 'Save Changes', 'wp-ever-accounting' ) }
						</Button>
					</Space>
				</>
			) }
		</Form>
	);
}

export default Company;
