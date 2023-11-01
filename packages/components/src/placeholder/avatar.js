/**
 * External dependencies
 */
import classNames from 'classnames';
/**
 * Internal dependencies
 */
import Element from './element';

function Avatar( {
	className,
	active,
	shape = 'circle',
	size = 'default',
	...props
} ) {
	const classes = classNames(
		'eac-placeholder',
		'eac-placeholder-element',
		className,
		{
			'eac-placeholder--active': active,
		}
	);

	return (
		<div className={ classes }>
			<Element
				element="eac-placeholder-avatar"
				shape={ shape }
				size={ size }
				{ ...props }
			/>
		</div>
	);
}

export default Avatar;
