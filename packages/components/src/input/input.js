/**
 * WordPress dependencies
 */
import { __experimentalInputControl as InputControl } from '@wordpress/components';
import { forwardRef } from '@wordpress/element';


const Input = forwardRef( ( { error, ...props }, ref ) => {
	return <InputControl { ...props } ref={ ref } />;
} );

export default Input;
