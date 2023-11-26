/**
 * External dependencies
 */
import { Formik, Form as FormikForm, Field as FormikField } from 'formik';
import { get, mapValues, isEmpty } from 'lodash';
/**
 * Internal dependencies
 */
import Fields from './fields';

const Form = ({ validate, validations, ...otherProps }) => (
	<Formik
		{...otherProps}
		validate={(values) => {
			if (validate) {
				return validate(values);
			}
			if (validations) {
				return (fieldValues, fieldValidators) => {
					const errors = {};

					Object.entries(fieldValidators).forEach(([fieldName, validators]) => {
						[validators].flat().forEach((validator) => {
							const errorMessage = validator(fieldValues[fieldName], fieldValues);
							if (errorMessage && !errors[fieldName]) {
								errors[fieldName] = errorMessage;
							}
						});
					});
					return errors;
				};
			}
			return {};
		}}
	/>
);

Form.Element = (props) => <FormikForm noValidate {...props} />;

Form.Field = mapValues(Fields, (FieldComponent) => ({ name, validate, ...props }) => (
	<FormikField name={name} validate={validate}>
		{({ field, form: { touched, errors, setFieldValue } }) => (
			<FieldComponent
				{...field}
				{...props}
				name={name}
				error={get(touched, name) && get(errors, name)}
				onChange={(value) => setFieldValue(name, value)}
			/>
		)}
	</FormikField>
));

Form.SectionTitle = ({ children }) => <h3 className="eac-form__section-title">{children}</h3>;

Form.initialValues = (data, getFieldValues) =>
	getFieldValues((key, defaultValue = '') => {
		const value = get(data, key);
		return value === undefined || value === null ? defaultValue : value;
	});

Form.handleAPIError = (error, form) => {
	if (error.data.fields) {
		form.setErrors(error.data.fields);
	} else {
		console.log(error);
	}
};

Form.is = {
	match:
		(testFn, message = '') =>
		(value, fieldValues) =>
			!testFn(value, fieldValues) && message,

	required: () => (value) => isEmpty(value) && __('This field is required', 'wp-ever-accounting'),

	minLength: (min) => (value) =>
		!!value && value.length < min && `Must be at least ${min} characters`,

	maxLength: (max) => (value) =>
		!!value && value.length > max && `Must be at most ${max} characters`,

	notEmptyArray: () => (value) =>
		Array.isArray(value) && value.length === 0 && 'Please add at least one item',

	email: () => (value) => !!value && !/.+@.+\..+/.test(value) && 'Must be a valid email',

	url: () => (value) =>
		!!value &&
		// eslint-disable-next-line no-useless-escape
		!/^(?:http(s)?:\/\/)?[\w.-]+(?:\.[\w\.-]+)+[\w\-\._~:/?#[\]@!\$&'\(\)\*\+,;=.]+$/.test(
			value
		) &&
		'Must be a valid URL',
	number: () => (value) =>
		!!value && isNaN(value) && __('Must be a number', 'wp-ever-accounting'),
	integer: () => (value) =>
		!!value &&
		!Number.isInteger(Number(value)) &&
		__('Must be an integer', 'wp-ever-accounting'),
	positive: () => (value) =>
		!!value && Number(value) <= 0 && __('Must be positive', 'wp-ever-accounting'),
	positiveOrZero: () => (value) =>
		!!value && Number(value) < 0 && __('Must be positive or zero', 'wp-ever-accounting'),
	price: () => (value) =>
		!!value &&
		!/^\d+(\.\d{1,2})?$/.test(value) &&
		__('Must be a valid price', 'wp-ever-accounting'),
	oneOf: (values) => (value) =>
		!values.includes(value) && __('Invalid value', 'wp-ever-accounting'),
};

export default Form;
