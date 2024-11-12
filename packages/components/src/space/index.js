/**
 * External dependencies
 */
import classNames from 'classnames';
/**
 * WordPress dependencies
 */
import { isValidElement } from '@wordpress/element';
import { toArray } from 'lodash';
/**
 * Internal dependencies
 */
import { SpaceContextProvider } from './context';
import Item from './item';
import './style.scss';

const Space = ( props ) => {
	const {
		className,
		direction = 'horizontal',
		size = 'small',
		align,
		split,
		wrap = false,
		flex,
		children,
		style,
		...otherProps
	} = props;

	const childNodes = toArray( children );
	let latestIndex = 0;
	const nodes = childNodes.map( ( child, i ) => {
		if ( ! isValidElement( child ) ) {
			return null;
		}
		latestIndex = i;
		const key = ( child && child.key ) || `space-${ i }`;
		return (
			<Item
				key={ key }
				direction={ direction }
				size={ size }
				align={ align }
				split={ split }
				wrap={ wrap }
				index={ i }
				latestIndex={ latestIndex }
			>
				{ typeof child === 'string' ? { child } : child }
			</Item>
		);
	} );

	if ( nodes.length === 0 ) {
		return null;
	}

	const styles = {
		flexWrap: wrap ? 'wrap' : null,
		flex: flex ? flex : null,
	};

	const classes = classNames( 'eac-space', className, {
		'eac-space--horizontal': direction === 'horizontal',
		'eac-space--vertical': direction === 'vertical',
		'eac-space--split': split,
		'eac-space--wrap': wrap,
		'eac-space--row-gap-small': size === 'small' && direction === 'vertical',
		'eac-space--row-gap-medium': size === 'medium' && direction === 'vertical',
		'eac-space--row-gap-large': size === 'large' && direction === 'vertical',
		'eac-space--column-gap-small': size === 'small' && direction === 'horizontal',
		'eac-space--column-gap-medium': size === 'medium' && direction === 'horizontal',
		'eac-space--column-gap-large': size === 'large' && direction === 'horizontal',
		[ `eac-space--align-${ align }` ]: align,
	} );

	return (
		<SpaceContextProvider value={ { latestIndex } }>
			<div className={ classes } { ...otherProps } style={ { ...style, ...styles } }>
				{ nodes }
			</div>
		</SpaceContextProvider>
	);
};

export default Space;
