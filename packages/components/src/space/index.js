/**
 * External dependencies
 */
import classNames from 'classnames';
/**
 * WordPress dependencies
 */
import { useMemo } from '@wordpress/element';
import { toArray } from 'lodash';
/**
 * Internal dependencies
 */
import { SpaceContextProvider } from './context';
import Item from './item';
import './style.scss';

const isPresetSize = ( size ) => {
	return [ 'small', 'medium', 'large' ].includes( size );
};

const isValidGapNumber = ( size ) => {
	if ( ! size ) {
		return false;
	}
	return typeof size === 'number' && ! Number.isNaN( size );
};

const Space = ( props ) => {
	const { size = 'small', align, className, children, direction = 'horizontal', split, style, wrap = false, styles, ...otherProps } = props;
	const [ horizontalSize, verticalSize ] = Array.isArray( size ) ? size : [ size, size ];
	const mergedAlign = align === undefined && direction === 'horizontal' ? 'center' : align;
	const childNodes = toArray( children, { keepEmpty: true } );
	const spaceContext = useMemo( () => ( { latestIndex } ), [ latestIndex ] );
	if ( childNodes.length === 0 ) {
		return null;
	}
	const isPresetVerticalSize = isPresetSize( verticalSize );
	const isPresetHorizontalSize = isPresetSize( horizontalSize );
	const isValidVerticalSize = isValidGapNumber( verticalSize );
	const isValidHorizontalSize = isValidGapNumber( horizontalSize );
	const gapStyle = {};
	if ( wrap ) {
		gapStyle.flexWrap = 'wrap';
	}
	if ( ! isPresetHorizontalSize && isValidHorizontalSize ) {
		gapStyle.columnGap = horizontalSize;
	}
	if ( ! isPresetVerticalSize && isValidVerticalSize ) {
		gapStyle.rowGap = verticalSize;
	}
	// Calculate latest one
	let latestIndex = 0;
	const nodes = childNodes.map( ( child, i ) => {
		if ( child !== null && child !== undefined ) {
			latestIndex = i;
		}
		return (
			<Item className="eac-space__item" index={ i } split={ split } key={ i }>
				{ child }
			</Item>
		);
	} );
	const classes = classNames( 'eac-space', className, {
		'eac-space--horizontal': direction === 'horizontal',
		'eac-space--vertical': direction === 'vertical',
		'eac-space--split': split,
		'eac-space--wrap': wrap,
		[ `eac-space--align-${ mergedAlign }` ]: mergedAlign,
		[ `eac-space--row-gap-${ verticalSize }` ]: isPresetVerticalSize,
		[ `eac-space--col-gap-${ horizontalSize }` ]: isPresetHorizontalSize,
	} );
	return (
		<div className={ classes } style={ { ...gapStyle, ...style } } { ...otherProps }>
			<SpaceContextProvider value={ spaceContext }>{ nodes }</SpaceContextProvider>
		</div>
	);
};

export default Space;
