/**
 * External dependencies
 */
import classNames from 'classnames';

function useElement( name, { style, size, shape, block } ) {
	const classes = classNames( 'eac-placeholder__element', {
		[ `eac-placeholder__element--${ name }` ]: name,
		'eac-placeholder__element--large': size === 'large',
		'eac-placeholder__element--small': size === 'small',
		'eac-placeholder__element--circle': shape === 'circle',
		'eac-placeholder__element--square': shape === 'square',
		'eac-placeholder__element--block': block,
	} );

	return {
		classes,
		style,
	};
}

export default useElement;
