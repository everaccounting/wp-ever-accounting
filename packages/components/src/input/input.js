/**
 * WordPress dependencies
 */
// eslint-disable-next-line @wordpress/no-unsafe-wp-apis
import { __experimentalInputControl as InputControl } from '@wordpress/components';
import { forwardRef } from '@wordpress/element';

/**
 * Internal dependencies
 */
import './style.scss';

const Input = forwardRef( ( props, ref ) => {
	return <InputControl { ...props } ref={ ref } />;
} );

export default Input;
