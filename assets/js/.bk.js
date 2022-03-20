/**
 * External dependencies
 */
import { Form } from '@eaccounting/components';
import { CORE_STORE_NAME } from '@eaccounting/data';
/**
 * WordPress dependencies
 */
import { useSelect } from '@wordpress/data';
/**
 * Internal dependencies
 */
import EnhancedForm from './form';
const { currency_codes } = eaccounting_i18n;

export default function App() {
	// const contact = useSelect( ( select ) => {
	// 	return select( CORE_STORE_NAME ).getEntityRecords( 'currencies' );
	// } );

	console.log( currency_codes );

	return (
		<div>
			<Form
				initialValues={ Form.initialValues(
					currency_codes.USD,
					( get ) => ( {
						name: get( 'name' ),
						code: get( 'code' ),
						rate: get( 'rate' ),
					} )
				) }
				validations={ {
					name: [ Form.is.required(), Form.is.maxLength( 100 ) ],
				} }
				onSubmit={ async ( values, form ) => {
					console.log( values );
					console.log( form );
					// try {
					// 	await updateProject( values );
					// 	await fetchProject();
					// 	toast.success( 'Changes have been saved successfully.' );
					// } catch ( error ) {
					// 	Form.handleAPIError( error, form );
					// }
				} }
				enableReinitialize={ true }
			>
				{({
					  errors,
					  handleBlur,
					  handleChange,
					  handleSubmit,
					  isSubmitting,
					  touched,
					  values,
					  setFieldValue
				  }) => (
					<form
						onSubmit={handleSubmit}
						{...rest}
					>
				<div>
					<Form.Field.Input name="name" label="Name" />
					<Form.Field.Select
						name="code"
						label="Code"
						options={ Object.values( currency_codes ).map(
							( currency ) => ( {
								value: currency.code,
								label: currency.name,
							} )
						) }
					/>
					<Form.Field.Input name="symbol" label="Symbol" />
					<button type="submit">Submit</button>
				</div>
						)}
			</Form>
		</div>
	);
}
