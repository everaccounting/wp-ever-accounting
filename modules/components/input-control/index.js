/**
 * WordPress dependencies
 */
import { forwardRef } from '@wordpress/element';
/**
 * External dependencies
 */
import { splitFormProps, useField, useFormContext } from 'react-form';
import PropTypes from 'prop-types';

const InputControl = (props, ref) => {
	const formInstance = useFormContext();
	console.log(formInstance);
	const { component = 'input', ...otherProps } = props;
	// Let's use splitFormProps to get form-specific props
	const [field, fieldOptions, rest] = splitFormProps(otherProps);

	// Use the useField hook with a field and field options
	// to access field state
	const {
		meta: { error },
		getInputProps,
		setValue,
	} = useField(field, fieldOptions);
	// Build the field
	const Comp = component;
	return (
		<Comp
			{...getInputProps({ ref, ...rest })}
			onChange={setValue}
			help={error}
		/>
	);
};
InputControl.propTypes = {
	// An array of columns, as objects.
	field: PropTypes.string.isRequired,
	component: PropTypes.any.isRequired,
};

export default forwardRef(InputControl);
