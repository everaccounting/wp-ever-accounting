/**
 * WordPress dependencies
 */
import { forwardRef, useState, useEffect } from '@wordpress/element';
const Portal = forwardRef( ( props, ref ) => {
	const { open, autoLock, autoDestroy = true, children } = props;
	const [ shouldRender, setShouldRender ] = useState( open );
	const mergedRender = shouldRender || open;

	// ====================== Should Render ======================
	useEffect( () => {
		if ( autoDestroy || open ) {
			setShouldRender( open );
		}
	}, [ open, autoDestroy ] );

	const [ innerContainer, setInnerContainer ] = useState( () =>
		getPortalContainer( getContainer )
	);
} );

export default Portal;
