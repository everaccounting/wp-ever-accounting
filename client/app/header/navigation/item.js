/**
 * External dependencies
 */
/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { Button } from '@wordpress/components';
import classnames from 'classnames';

export default function Item( { id, icon, title, className, onClick } ) {
	return (
		<Button
			role="button"
			className={ className }
			key={ id }
			id={ id }
			onClick={ onClick }
		>
			{ icon }
			{ title }{ ' ' }
		</Button>
	);
}
