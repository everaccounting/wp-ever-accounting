/**
 * WordPress dependencies
 */
import { ToggleControl } from '@wordpress/components';
import { forwardRef } from '@wordpress/element';

const Toggle = forwardRef( ( props, ref ) => {
	return <ToggleControl { ...props } ref={ ref } />;
} );

export default Toggle;
