/**
 * WordPress dependencies
 */
import { useRef, useEffect } from '@wordpress/element';

/**
 * Internal dependencies
 */

import isOutside from './is-outside';

/**
 * onOutside callback.
 *
 * @callback requestCallback
 * @param {Object} ev Event handler object
 */

/**
 * Wraps a component and detects clicks outside of that component.
 *
 * @param {Object}          props           - Component props
 * @param {Object}          props.children  - Child components
 * @param {string}          props.className - Class name for the wrapper
 * @param {requestCallback} props.onOutside - Callback when user clicks outside of the wrapper
 */
function ClickOutside( props ) {
	const containerRef = useRef( null );
	const { children, onOutside, className } = props;
	const outside = ( ev ) => {
		if ( isOutside( ev, containerRef.current ) || ev.key === 'Escape' ) {
			onOutside( ev );
		}
	};

	/*eslint-disable @wordpress/no-global-event-listener, react-hooks/exhaustive-deps */
	useEffect( () => {
		addEventListener( 'mousedown', outside );
		addEventListener( 'keydown', outside );

		return () => {
			removeEventListener( 'mousedown', outside );
			removeEventListener( 'keydown', outside );
		};
	}, [] );
	/* eslint-enable @wordpress/no-global-event-listener, react-hooks/exhaustive-deps */

	return (
		<div className={ className } ref={ containerRef }>
			{ children }
		</div>
	);
}

ClickOutside.isOutside = isOutside;

export default ClickOutside;
