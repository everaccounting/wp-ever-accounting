/**
 * WordPress dependencies
 */
import { useMemo } from '@wordpress/element';
/**
 * External dependencies
 */
import classNames from 'classnames';

function Element( props ) {
	const { element, className, style, size, shape } = props;
	const sizeCls = classNames( {
		[ `${ element }--lg` ]: size === 'large',
		[ `${ element }--sm` ]: size === 'small',
	} );

	const shapeCls = classNames( {
		[ `${ element }--circle` ]: shape === 'circle',
		[ `${ element }--square` ]: shape === 'square',
	} );

	const sizeStyle = useMemo(
		() =>
			typeof size === 'number'
				? {
						width: size,
						height: size,
						lineHeight: `${ size }px`,
				  }
				: {},
		[ size ]
	);

	return (
		<span
			className={ classNames( element, className, sizeCls, shapeCls ) }
			style={ { ...sizeStyle, ...style } }
		/>
	);
}

export default Element;
