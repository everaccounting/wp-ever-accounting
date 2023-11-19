/**
 * External dependencies
 */
import { Form, Button, Panel, Text, Space } from '@eac/components';
/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';

function Options() {
	return (
		<Form
			initialValues={ {
				company_name: 'test',
			} }
			validations={ {
				company_name: Form.is.required(),
				company_email: [ Form.is.required(), Form.is.email() ],
			} }
		>
			<>
				<Text as="p" size="14" lineHeight="32px">
					{ __( 'Tax Options', 'wp-ever-accounting' ) }
				</Text>
				<Text as="p" style={ { marginBottom: '20px' } } color="gray">
					{ __(
						'Setup your tax options. How and when you want to apply taxes to your invoices.',
						'wp-ever-accounting'
					) }
				</Text>
				<Space size="medium" direction="vertical" style={ { display: 'flex' } }>
					<Form.Field.Checkbox
						name="tax_enabled"
						label={ __( 'Enable Tax', 'wp-ever-accounting' ) }
						help={ __( 'Enable tax rates and calculations.', 'wp-ever-accounting' ) }
					/>
					<Form.Field.Checkbox
						name="tax_subtotal_rounding"
						label={ __( 'Rounding', 'wp-ever-accounting' ) }
						help={ __(
							'Round tax at subtotal level, instead of rounding per tax rate.',
							'wp-ever-accounting'
						) }
					/>
					<Form.Field.Select
						name="prices_include_tax"
						label={ __( 'Prices include tax', 'wp-ever-accounting' ) }
						help={ __(
							'Select whether prices entered include tax or not.',
							'wp-ever-accounting'
						) }
						options={ [
							{
								value: 'yes',
								label: __(
									'Yes, I will enter prices inclusive of tax',
									'wp-ever-accounting'
								),
							},
							{
								value: 'no',
								label: __(
									'No, I will enter prices exclusive of tax',
									'wp-ever-accounting'
								),
							},
						] }
					/>
					<Form.Field.Select
						name="tax_display_totals"
						label={ __( 'Display tax totals', 'wp-ever-accounting' ) }
						help={ __(
							'Select how you want to display tax totals.',
							'wp-ever-accounting'
						) }
						options={ [
							{
								value: 'single',
								label: __( 'As a single total', 'wp-ever-accounting' ),
							},
							{
								value: 'itemized',
								label: __( 'Itemized', 'wp-ever-accounting' ),
							},
						] }
					/>

					<Button type="submit" isPrimary>
						{ __( 'Save Changes', 'wp-ever-accounting' ) }
					</Button>
				</Space>
			</>
		</Form>
	);
}

export default Options;
