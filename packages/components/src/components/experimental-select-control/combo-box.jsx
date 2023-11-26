/**
 * External dependencies
 */
import { useRef, forwardRef } from 'react';
import classNames from 'classnames';
/**
 * WordPress dependencies
 */
import { Icon, chevronDown } from '@wordpress/icons';
const ToggleButton = forwardRef( ( props, ref ) => {
	// using forwardRef here because getToggleButtonProps injects a ref prop
	return (
		<button
			className="woocommerce-experimental-select-control__combox-box-toggle-button"
			{ ...props }
			ref={ ref }
		>
			<Icon icon={ chevronDown } />
		</button>
	);
} );
export const ComboBox = ( {
	children,
	comboBoxProps,
	getToggleButtonProps = () => ( {} ),
	inputProps,
	suffix,
	showToggleButton,
} ) => {
	const inputRef = useRef( null );
	const maybeFocusInput = ( event ) => {
		if ( ! inputRef || ! inputRef.current ) {
			return;
		}
		if ( document.activeElement !== inputRef.current ) {
			event.preventDefault();
			inputRef.current.focus();
			event.stopPropagation();
		}
	};
	return (
		// Disable reason: The click event is purely for accidental clicks around the input.
		// Keyboard users are still able to tab to and interact with elements in the combobox.
		/* eslint-disable jsx-a11y/no-static-element-interactions, jsx-a11y/click-events-have-key-events */
		<div
			className={ classNames( 'woocommerce-experimental-select-control__combo-box-wrapper', {
				'woocommerce-experimental-select-control__combo-box-wrapper--disabled':
					inputProps.disabled,
			} ) }
			onMouseDown={ maybeFocusInput }
		>
			<div className="woocommerce-experimental-select-control__items-wrapper">
				{ children }
				<div
					{ ...comboBoxProps }
					className="woocommerce-experimental-select-control__combox-box"
				>
					<input
						{ ...inputProps }
						ref={ ( node ) => {
							inputRef.current = node;
							if ( typeof inputProps.ref === 'function' ) {
								inputProps.ref( node );
							}
						} }
					/>
				</div>
			</div>
			{ suffix && (
				<div className="woocommerce-experimental-select-control__suffix">{ suffix }</div>
			) }
			{ showToggleButton && <ToggleButton { ...getToggleButtonProps() } /> }
		</div>
	);
};
