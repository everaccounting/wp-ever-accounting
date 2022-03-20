/**
 * External dependencies
 */
import PropTypes from 'prop-types';
import { Formik, Form as FormikForm, Field as FormikField, withFormik } from 'formik';
import { get, mapValues } from 'lodash';

/**
 * Internal dependencies
 */
import toast from '../toast';
import Field from './field';

const propTypes = {
	validate: PropTypes.func,
	validations: PropTypes.object,
	validateOnBlur: PropTypes.bool,
};

const defaultProps = {
	validate: undefined,
	validations: undefined,
	validateOnBlur: false,
};

const Form = ( {
	validate,
	validations,
	initialValues = {},
	...otherProps
} ) => (
	<Formik
		initialValues={ initialValues }
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

Form.Field = mapValues(
	Field,
	( FieldComponent ) => ( { name, validate, ...props } ) => (
		<FormikField name={ name } validate={ validate }>
			{ ( { field, form: { touched, errors, setFieldValue } } ) => (
				<FieldComponent
					{ ...field }
					name={ name }
					error={ get( touched, name ) && get( errors, name ) }
					onChange={ ( value ) => setFieldValue( name, value ) }
					{ ...props }
				/>
			) }
		</FormikField>
	)
);

Form.initialValues = ( data, getFieldValues ) =>
	getFieldValues( ( key, defaultValue = '' ) => {
		const value = get( data, key );
		return value === undefined || value === null ? defaultValue : value;
	} );

Form.handleAPIError = ( error, form ) => {
	if ( error.data.fields ) {
		form.setErrors( error.data.fields );
	} else {
		toast.error( error );
	}
};

Form.is = {
	match: ( testFn, message = '' ) => ( value, fieldValues ) =>
		! testFn( value, fieldValues ) && message,

	required: () => ( value ) =>
		isNilOrEmptyString( value ) && 'This field is required',

	minLength: ( min ) => ( value ) =>
		!! value &&
		value.length < min &&
		`Must be at least ${ min } characters`,

	maxLength: ( max ) => ( value ) =>
		!! value && value.length > max && `Must be at most ${ max } characters`,

	notEmptyArray: () => ( value ) =>
		Array.isArray( value ) &&
		value.length === 0 &&
		'Please add at least one item',

	email: () => ( value ) =>
		!! value && ! /.+@.+\..+/.test( value ) && 'Must be a valid email',

	url: () => ( value ) =>
		!! value &&
		// eslint-disable-next-line no-useless-escape
		! /^(?:http(s)?:\/\/)?[\w.-]+(?:\.[\w\.-]+)+[\w\-\._~:/?#[\]@!\$&'\(\)\*\+,;=.]+$/.test(
			value
		) &&
		'Must be a valid URL',
};

const isNilOrEmptyString = ( value ) =>
	value === undefined || value === null || value === '';
const generateErrors = ( fieldValues, fieldValidators ) => {
	const errors = {};

	Object.entries( fieldValidators ).forEach(
		( [ fieldName, validators ] ) => {
			[ validators ].flat().forEach( ( validator ) => {
				const errorMessage = validator(
					fieldValues[ fieldName ],
					fieldValues
				);
				if ( errorMessage && ! errors[ fieldName ] ) {
					errors[ fieldName ] = errorMessage;
				}
			} );
		}
	);
	return errors;
};

Form.propTypes = propTypes;
Form.defaultProps = defaultProps;
Form.generateErrors = generateErrors;
Form.withFormik = withFormik;

export default Form;
