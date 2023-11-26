/**
 * External dependencies
 */
import classNames from 'classnames';

/**
 * WordPress dependencies
 */
import { useInstanceId } from '@wordpress/compose';
import { useState, forwardRef } from '@wordpress/element';
import { BaseControl, VisuallyHidden } from '@wordpress/components';
/**
 * Internal dependencies
 */
import { Container, Prefix, Suffix, BackdropUI } from './styles';

/**
 * Internal dependencies
 */

const noop = () => {};

function useUniqueId( idProp ) {
	const instanceId = useInstanceId( Select );
	const id = `inspector-select-control-${ instanceId }`;

	return idProp || id;
}

function UnforwardedSelect( props, ref ) {
	const {
		className,
		disabled = false,
		help,
		hideLabelFromVision,
		id: idProp,
		label,
		multiple = false,
		onBlur = noop,
		onChange,
		onFocus = noop,
		options = [],
		size = 'default',
		value: valueProp,
		labelPosition = 'top',
		children,
		prefix,
		suffix,
		noMarginBottom = false,
		...restProps
	} = props;
	const [ isFocused, setIsFocused ] = useState( false );
	const id = useUniqueId( idProp );
	const helpId = help ? `${ id }__help` : undefined;
	const handleOnBlur = ( event ) => {
		setIsFocused( false );
		onBlur( event );
	};
	const handleOnFocus = ( event ) => {
		setIsFocused( true );
		onFocus( event );
	};
	return (
		<BaseControl
			label={ label }
			help={ help }
			id={ id }
			__nextHasNoMarginBottom={ noMarginBottom }
		>
			<Container
				className="components-input-control__container"
				disabled={ disabled }
				isFocused={ isFocused }
			>
				{ prefix && (
					<Prefix className="components-input-control__prefix">{ prefix }</Prefix>
				) }
				<input
					className="components-input-control__select"
					ref={ ref }
					onBlur={ handleOnBlur }
					onFocus={ handleOnFocus }
				/>
				{ suffix && (
					<Suffix className="components-input-control__suffix">{ suffix }</Suffix>
				) }
				<BackdropUI
					aria-hidden="true"
					className="components-input-control__backdrop"
					disabled={ disabled }
					isFocused={ isFocused }
				/>
			</Container>
		</BaseControl>
	);
}

const Select = forwardRef( UnforwardedSelect );

export default Select;
