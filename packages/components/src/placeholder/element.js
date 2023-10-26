/**
 * WordPress dependencies
 */
import { useMemo } from '@wordpress/element';
/**
 * External dependencies
 */
import classNames from 'classnames';

function Element( props ) {
	const { className, style, size, shape } = props;
	const sizeCls = classNames( {
		[ `${ className }--lg` ]: size === 'large',
		[ `${ className }--sm` ]: size === 'small',
	} );

	const shapeCls = classNames( {
		[ `${ className }--circle` ]: shape === 'circle',
		[ `${ className }--square` ]: shape === 'square',
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

	return <span className={ classNames( className, sizeCls, shapeCls ) } style={ { ...sizeStyle, ...style } } />;
}

export default Element;
