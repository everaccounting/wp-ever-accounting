/**
 * External dependencies
 */
import PropTypes from 'prop-types';
import { uniqueId } from 'lodash';
/**
 * WordPress dependencies
 */
import { TextControl, TextareaControl } from '@wordpress/components';
/**
 * Internal dependencies
 */

import SelectControl from '../select-control';
// import Textarea from 'shared/components/Textarea';
// import TextEditor from 'shared/components/TextEditor';
// import DatePicker from 'shared/components/DatePicker';

/**
 * Internal dependencies
 */
import { StyledField, FieldLabel, FieldTip, FieldError } from './styles';

const propTypes = {
	className: PropTypes.string,
	label: PropTypes.string,
	tip: PropTypes.string,
	error: PropTypes.string,
	name: PropTypes.string,
};

const defaultProps = {
	className: undefined,
	label: undefined,
	tip: undefined,
	error: undefined,
	name: undefined,
};

const generateField = ( FormComponent ) => {
	const FieldComponent = ( {
		className,
		label,
		tip,
		error,
		name,
		...otherProps
	} ) => {
		const fieldId = uniqueId( 'form-field-' );
		return (
			<StyledField
				className={ className }
				hasLabel={ !! label }
				data-testid={ name ? `form-field:${ name }` : 'form-field' }
			>
				{ label && (
					<FieldLabel htmlFor={ fieldId }>{ label }</FieldLabel>
				) }
				<FormComponent id={ fieldId } name={ name } { ...otherProps } />
				{ tip && <FieldTip>{ tip }</FieldTip> }
				{ error && <FieldError>{ error }</FieldError> }
			</StyledField>
		);
	};

	FieldComponent.propTypes = propTypes;
	FieldComponent.defaultProps = defaultProps;

	return FieldComponent;
};

export default {
	Input: generateField( TextControl ),
	Select: generateField( SelectControl ),
	Textarea: generateField( TextareaControl ),
	// Textarea: generateField( Textarea ),
	// TextEditor: generateField( TextEditor ),
	// DatePicker: generateField( DatePicker ),
};
