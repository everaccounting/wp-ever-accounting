/**
 * WordPress dependencies
 */
import { TextControl } from '@wordpress/components';
import { forwardRef } from '@wordpress/element';

const Input = forwardRef( ( props, ref ) => {
	return <TextControl { ...props } ref={ ref } />;
} );

export default Input;
