/**
 * External dependencies
 */
import classnames from 'classnames';

/**
 * WordPress dependencies
 */
import { useState, useRef, forwardRef } from '@wordpress/element';
import { useInstanceId } from '@wordpress/compose';
import { BaseControl } from '@wordpress/components';
import { TAB, ESCAPE, ENTER } from '@wordpress/keycodes';

/**
 * Internal dependencies
 */
import './style.scss';
import { StyledSelect } from './styles';

const noop = () => {};

function useUniqueId( idProp ) {
	const instanceId = useInstanceId( Select );
	const id = `inspector-select-control-${ instanceId }`;

	return idProp || id;
}

function UnforwardedSelect( props, ref ) {
	const {
		className,
		variant = 'empty',
		dropdownWidth,
		name,
		value: propsValue,
		defaultValue,
		placeholder,
		invalid,
		options,
		onChange,
		onCreate,
		isMulti,
		withClearValue,
	} = props;
	const [ isFocused, setIsFocused ] = useState( false );

	const handleOnFocus = ( event ) => {
		props?.onFocus?.( event );
		setIsFocused( true );
	};
	const handleOnBlur = ( event ) => {
		props?.onBlur?.( event );
		setIsFocused( false );
	};

	const classes = classnames( 'eac-select-control', className, {
		[ `eac-select-control--${ variant }` ]: variant,
	} );

	return (
		<StyledSelect
			className={ classes }
			variant={ variant }
			onFocus={ handleOnFocus }
			onBlur={ handleOnBlur }
			tabIndex="0"
			ref={ ref }
		>
			<option value="1">1</option>
		</StyledSelect>
	);
}

const Select = forwardRef( UnforwardedSelect );

export default Select;
