/**
 * Internal dependencies
 */
import Input from '../input';
/**
 * External dependencies
 */
import { uniqueId } from 'lodash';
import classNames from 'classnames';

const generateField = ( FormComponent ) => {
	const FieldComponent = ( { className, error, ...otherProps } ) => {
		const classes = classNames( 'eac-field', className, {
			'eac-field--error': error,
		} );

		// when we got error we will pass that as help prop to the FormComponent.
		if ( error ) {
			otherProps.help = (
				<>
					{ otherProps?.help ? otherProps.help : '' }
					<span className="eac-field__error">{ error }</span>
				</>
			);
		}

		const id = uniqueId( 'eac-field__input-' );
		return (
			<FormComponent
				className={ classes }
				id={ id }
				name={ name }
				{ ...otherProps }
			/>
		);
	};

	FieldComponent.displayName = 'FieldComponent';
	return FieldComponent;
};
export default {
	Input: generateField( Input ),
	Select: generateField( Input.Select ),
	Checkbox: generateField( Input.Checkbox ),
	Radio: generateField( Input.Radio ),
	Textarea: generateField( Input.Textarea ),
	Switch: generateField( Input.Switch ),
};
