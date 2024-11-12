/**
 * WordPress dependencies
 */
import { CheckboxControl } from '@wordpress/components';
import { forwardRef } from '@wordpress/element';

const Checkbox = forwardRef( ( props, ref ) => {
	return <CheckboxControl { ...props } ref={ ref } />;
} );

export default Checkbox;
