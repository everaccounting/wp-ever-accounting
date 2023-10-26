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

	return (
		<>
			<div className={ className } style={ style }>
				{ children }
			</div>
			{ index < latestIndex && split && <span className={ `${ className }-split` }>{ split }</span> }
		</>
	);
};

export default Item;
