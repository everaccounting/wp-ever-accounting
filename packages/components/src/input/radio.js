/**
 * WordPress dependencies
 */
import { RadioControl } from '@wordpress/components';
import { forwardRef } from '@wordpress/element';

const Radio = forwardRef( ( props, ref ) => {
	return <RadioControl { ...props } ref={ ref } />;
} );

export default Radio;
