/**
 * WordPress dependencies
 */
/**
 * External dependencies
 */
import { NumericFormat } from 'react-number-format';
import { forwardRef } from '@wordpress/element';
import { BaseControl } from '@wordpress/components';

/**
 * Internal dependencies
 */
import { Container, Prefix, Suffix, BackdropUI } from './styles';

const Amount = forwardRef( ( { label, help, ...props }, ref ) => {
	const { decimalScale = 2, fixedDecimalScale = true, thousandSeparator = true } = props;
	return (
		<BaseControl label={ label } help={ help }>
			<Container>
				<NumericFormat
					{ ...props }
					ref={ ref }
					className="components-input-control__input"
					decimalScale={ decimalScale }
					fixedDecimalScale={ fixedDecimalScale }
					thousandSeparator={ thousandSeparator }
				/>
				<BackdropUI
					aria-hidden="true"
					className="components-input-control__backdrop"
					disabled={ props.disabled }
					isFocused={ props.isFocused }
				/>
			</Container>
		</BaseControl>
	);
} );

export default Amount;
