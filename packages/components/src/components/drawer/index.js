/**
 * External dependencies
 */
import classnames from 'classnames';
/**
 * WordPress dependencies
 */
import { Modal } from '@wordpress/components';

/**
 * Internal dependencies
 */
import './style.scss';
import { useState, useMemo, useEffect, useContext } from '@wordpress/element';
import { DrawerContext } from './context';

function Drawer( props ) {
	const { className, onClose, size, style, children, ...rest } = props;
	const [ pushed, setPushed ] = useState( false );
	const parentContext = useContext( DrawerContext );
	const pushDistance = parentContext?.pushDistance || 180;
	const mergedContext = useMemo(
		() => ( {
			pushDistance,
			push: () => {
				setPushed( true );
			},
			pull: () => {
				setPushed( false );
			},
		} ),
		[ pushDistance ]
	);

	useEffect( () => {
		if ( pushed ) {
			const drawers = document.querySelectorAll( '.eac-drawer' );
			drawers.forEach( ( drawer ) => {
				drawer.style.transform = `translateX(${ -pushDistance }px)`;
			} );
			const focusedDrawer = document.querySelector( '.eac-drawer:last-child' );
			if ( focusedDrawer ) {
				focusedDrawer.style.transform = 'translateX(0)';
			}
		}
	}, [ pushed, pushDistance ] );

	const wrapperStyle = {};
	if ( pushed ) {
		wrapperStyle.transform = `translateX(${ -pushDistance }px)`;
	}

	console.log(wrapperStyle);

	const classes = classnames( 'eac-drawer', className );

	return (
		<DrawerContext.Provider value={ mergedContext }>
			<Modal
				className={ classes }
				{ ...rest }
				onRequestClose={ onClose }
				style={ wrapperStyle }
			>
				<div className="eac-drawer__body">{ children }</div>
			</Modal>
		</DrawerContext.Provider>
	);
}

export default Drawer;
