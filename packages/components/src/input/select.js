/**
 * WordPress dependencies
 */
import { SelectControl } from '@wordpress/components';
import { forwardRef } from '@wordpress/element';

/**
 * Internal dependencies
 */
import './style.scss';

const Select = forwardRef( ( { error, ...props }, ref ) => {
	return <SelectControl { ...props } ref={ ref } />;
} );

export default Select;
