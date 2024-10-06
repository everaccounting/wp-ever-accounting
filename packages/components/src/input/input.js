/**
 * WordPress dependencies
 */
import { __experimentalInputControl as InputControl } from '@wordpress/components';
import { forwardRef } from '@wordpress/element';
/**
 * External dependencies
 */
import classNames from 'classnames';
/**
 * Internal dependencies
 */
import './style.scss';

const Input = forwardRef( ( { error, ...props }, ref ) => {
	const classes = classNames( 'eac-input', {
		'eac-input--error': error,
	} );

	return <InputControl className={ classes } { ...props } ref={ ref } />;
} );

export default Input;
