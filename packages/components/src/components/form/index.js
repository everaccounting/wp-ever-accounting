/**
 * External dependencies
 */
import { Formik, Form as FormikForm, Field as FormikField } from 'formik';
import { get, mapValues } from 'lodash';
/**
 * Internal dependencies
 */
import Fields from './fields';
import { is, generateErrors } from '../../utils';

const Form = ( { validate, validations, ...otherProps } ) => (
	<Formik
		{ ...otherProps }
		validate={ ( values ) => {
			if ( validate ) {
				return validate( values );
			}
			if ( validations ) {
				return generateErrors( values, validations );
			}
			return {};
		} }
	/>
);

Form.Element = ( props ) => <FormikForm noValidate { ...props } />;

Form.Field = mapValues( Fields, ( FieldComponent ) => ( { name, validate, ...props } ) => (
	<FormikField name={ name } validate={ validate }>
		{ ( { field, form: { touched, errors, setFieldValue } } ) => (
			<FieldComponent
				{ ...field }
				{ ...props }
				name={ name }
				error={ get( touched, name ) && get( errors, name ) }
				onChange={ ( value ) => setFieldValue( name, value ) }
			/>
		) }
	</FormikField>
) );

Form.initialValues = ( data, getFieldValues ) =>
	getFieldValues( ( key, defaultValue = '' ) => {
		const value = get( data, key );
		return value === undefined || value === null ? defaultValue : value;
	} );

Form.handleAPIError = ( error, form ) => {
	if ( error.data.fields ) {
		form.setErrors( error.data.fields );
	} else {
		console.log( error );
	}
};

Form.is = is;

export default Form;
