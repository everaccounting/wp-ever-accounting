/**
 * External dependencies
 */
import { Form } from '@eaccounting/components';
import { CORE_STORE_NAME } from '@eaccounting/data';
/**
 * WordPress dependencies
 */
import { useSelect } from '@wordpress/data';
import React from ".";
/**
 * Internal dependencies
 */
const { currency_codes } = eaccounting_i18n;

export default function App() {
	// const contact = useSelect( ( select ) => {
	// 	return select( CORE_STORE_NAME ).getEntityRecords( 'currencies' );
	// } );

	console.log( currency_codes );

	return (
		<div>
			<Form
				initialValues={ {
					code: 'USD',
					symbol: '$',
					name: 'US Dollar',
					rate: '1.0',
					decimal_places: '2',
					thousands_separator: ',',
					decimal_separator: '.',
					prefix: '',
					suffix: '',
				} }
				validations={ {
					name: [ Form.is.required(), Form.is.maxLength( 100 ) ],
				} }
				onSubmit={ async ( values, form ) => {
					console.log( values );
					console.log( form );
					try {
						await updateProject( values );
						await fetchProject();
						toast.success( 'Changes have been saved successfully.' );
					} catch ( error ) {
						Form.handleAPIError( error, form );
					}
				} }
				enableReinitialize={ true }
			>
				{ ( {
					errors,
					handleBlur,
					handleChange,
					handleSubmit,
					isSubmitting,
					touched,
					values,
					setFieldValue,
				} ) => (
					<form onSubmit={ handleSubmit }>
						<Form.Field.Select
							name="code"
							label="Code"
							options={ Object.values( currency_codes ).map(
								( currency ) => ( {
									value: currency.code,
									label: currency.name,
								} )
							) }
							// onCreate={ ( value, cb ) => {
							// 	console.log( value );
							// 	return cb(value);
							// } }
							onChange={ ( val, field ) => {
								console.log( 'field', field );
								setFieldValue( 'code', val );
								setFieldValue( 'symbol', 'B' );
								console.log( val );
							} }
						/>
						<Form.Field.Select
							name="reporterId"
							label="Reporter"
							options={userOptions(project)}
							renderOption={renderUser(project)}
							renderValue={renderUser(project)}
						/>
						<Form.Field.Input name="symbol" label="Symbol" />
						{ JSON.stringify( values ) }
						<button
							type="submit"
							disabled={ isSubmitting }
							onClick={ handleSubmit }
						>
							Submit
						</button>
					</form>
				) }
			</Form>
		</div>
	);
}
