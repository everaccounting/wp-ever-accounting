/**
 * WordPress dependencies
 */
import { Button as ButtonComponent } from '@wordpress/components';
/**
 * External dependencies
 */
import classnames from 'classnames';

function Button( props ) {
	const { className, children, ...rest } = props;
	const classes = classnames( className, 'eac-dropdown__button' );
	return (
		<ButtonComponent { ...rest } className={ classes }>
			{ children }
		</ButtonComponent>
	);
}

export default Button;
