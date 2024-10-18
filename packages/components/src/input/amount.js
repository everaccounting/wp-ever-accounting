/**
 * WordPress dependencies
 */
import { forwardRef, memo } from '@wordpress/element';
import { __experimentalInputControl as InputControl } from '@wordpress/components';
/**
 * External dependencies
 */
// const { InputNumberFormat } = lazy( () => import( '@react-input/number-format' ) );
import { useNumberFormat } from '@react-input/number-format';

const Amount = forwardRef(
	(
		{ label, help, placeholder, prefix, suffix, disabled, value, currency = 'USD', ...props },
		ref
	) => {
		const inputRef = useNumberFormat( { format: 'currency', currency, ...props } );
		return (
			<InputControl
				{ ...{ label, help, placeholder, prefix, suffix, disabled, value } }
				label={ label }
				help={ help }
				prefix={ prefix }
				suffix={ suffix }
				disabled={ disabled }
				value={ value }
				ref={ ( element ) => {
					inputRef.current = element;
					if ( typeof ref === 'function' ) {
						ref( element );
					} else if ( ref ) {
						ref.current = element;
					}
				} }
			/>
		);
	}
);

export default memo( Amount );
