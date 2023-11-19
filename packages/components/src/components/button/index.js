/**
 * WordPress dependencies
 */
import { Button as ButtonComponent } from '@wordpress/components';
import { forwardRef } from '@wordpress/element';
/**
 * External dependencies
 */
import classNames from 'classnames';

/**
 * Internal dependencies
 */
import './style.scss';

const Button = ( props, ref ) => {
	const { className, ...resProps } = props;
	const classes = classNames( 'eac-button', props.className );
	return <ButtonComponent className={ classes } { ...resProps } ref={ ref } />;
};

export default forwardRef( Button );
