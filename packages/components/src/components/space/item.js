/**
 * External dependencies
 */
import classNames from 'classnames';
/**
 * WordPress dependencies
 */
import { useContext } from '@wordpress/element';
/**
 * Internal dependencies
 */
import { SpaceContext } from './context';

const Item = ( { className, index, children, split, style } ) => {
	const { latestIndex } = useContext( SpaceContext );

	if ( children === null || children === undefined ) {
		return null;
	}

	const classes = classNames( 'eac-space__item', className );

	return (
		<>
			<div className={ classes } style={ style }>
				{ children }
			</div>
			{ index < latestIndex && split && <span className={ `${ className }-split` }>{ split }</span> }
		</>
	);
};

export default Item;
