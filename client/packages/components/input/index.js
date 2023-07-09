import PropTypes from 'prop-types';
import { BaseControl } from '@wordpress/components';
import { forwardRef } from '@wordpress/element';
import { withInstanceId } from '@wordpress/compose';
import classnames from 'classnames';

export const Input = forwardRef((props, ref) => {
	const {
		label,
		help,
		className,
		onChange,
		prefix,
		suffix,
		required,
		instanceId,
		type='text',
		...restProps
	} = props;

	const id = `input-${instanceId}`;

	const classes = classnames('eac-input', {
		required: !!required,
	});
	const inputClasses = classnames('eac-input__field', className);

	return (
		<BaseControl label={label} id={id} help={help} className={classes}>
			<input
				id={id}
				onChange={(event) => onChange(event.target.value)}
				required={required}
				ref={ref}
				className={inputClasses}
				type={type}
				{...restProps}
			/>
		</BaseControl>
	);
});

Input.defaultProps = {
	value: '',
};

Input.propTypes = {
	label: PropTypes.string,
	help: PropTypes.string,
	value: PropTypes.string,
	className: PropTypes.string,
	onChange: PropTypes.func.isRequired,
	prefix: PropTypes.node,
	suffix: PropTypes.node,
	required: PropTypes.bool,
};

export default withInstanceId(Input);
