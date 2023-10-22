/**
 * WordPress dependencies
 */
import { TextareaControl } from '@wordpress/components';
import { forwardRef } from '@wordpress/element';

const Textarea = forwardRef( ( props, ref ) => {
	return <TextareaControl { ...props } ref={ ref } />;
} );

export default Textarea;
