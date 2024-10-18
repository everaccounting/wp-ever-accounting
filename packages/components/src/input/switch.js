/**
 * WordPress dependencies
 */
import { ToggleControl } from '@wordpress/components';
import { forwardRef } from '@wordpress/element';

const Switch = forwardRef( ( props, ref ) => {
	return <ToggleControl { ...props } ref={ ref } />;
} );

export default Switch;
