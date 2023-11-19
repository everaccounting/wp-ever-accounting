/**
 * WordPress dependencies
 */
import { SelectControl } from '@wordpress/components';
import { forwardRef } from '@wordpress/element';

const Select = forwardRef( ( props, ref ) => {
	return <SelectControl { ...props } ref={ ref } />;
} );

export default Select;
