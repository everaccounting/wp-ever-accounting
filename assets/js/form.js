/**
 * External dependencies
 */
import { Form, Field, FieldArray, withFormik } from 'formik';

function FormDemo( { values, setFieldValue, handleChange } ) {
	return (
		<div className="formik-wrapper">
			<Form>
				<FieldArray
					name="fields"
					render={ () => {
						return (
							<>
								<Field
									type="text"
									name="price"
									placeholder="Write something"
									onChange={ ( e ) => {
										handleChange( e );
										values.amount = values.quantity
											? e.target.value * values.quantity
											: 0;
									} }
								/>
								<br />
								<Field
									type="text"
									name="quantity"
									placeholder="Write something"
									onChange={ ( e ) => {
										handleChange( e );
										values.amount = values.price
											? e.target.value * values.price
											: 0;
									} }
								/>
								<br />
								<Field
									type="text"
									name="amount"
									placeholder="Write something"
								/>
							</>
						);
					} }
				/>
			</Form>
		</div>
	);
}

const EnhancedForm = withFormik( {
	enableReinitialize: true,
	mapPropsToValues: ( { fields } ) => ( {
		fields: fields || [ '' ],
	} ),
	handleSubmit: ( values, actions ) => {
		console.log( values );
		actions.setSubmitting( false );
	},
} )( FormDemo );
export default EnhancedForm;
