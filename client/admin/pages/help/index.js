/**
 * External dependencies
 */
import { SectionHeader, Form } from '@eac/components';
/**
 * WordPress dependencies
 */
import { Button } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

function Help() {
	return (
		<>
			<SectionHeader title={ __( 'Help', 'wp-ever-accounting' ) } />
			<Form
				enableReinitialize
				initialValues={ {
					name: 'John Doe',
				} }
				onSubmit={ async ( values, form ) => {
					console.log( values );
				} }
				validations={ {
					name: Form.is.required(),
				} }
			>
				{ ( { isValid, dirty, values, errors } ) => (
					<>
						<Form.Field.Input
							name="name"
							label={ __( 'Name', 'wp-ever-accounting' ) }
							help={ __(
								'Enter your name',
								'wp-ever-accounting'
							) }
						/>
						{ JSON.stringify( values ) }
						{ JSON.stringify( errors ) }
						<Button
							isPrimary
							type="submit"
							disabled={ ! isValid && ! dirty }
						>
							{ __( 'Save', 'wp-ever-accounting' ) }
						</Button>
					</>
				) }
			</Form>
		</>
	);
}

export default Help;
