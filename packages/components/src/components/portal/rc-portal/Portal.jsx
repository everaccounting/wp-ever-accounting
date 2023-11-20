/**
 * External dependencies
 */
import * as React from 'react';
import { createPortal } from 'react-dom';
import canUseDom from 'rc-util/lib/Dom/canUseDom';
import warning from 'rc-util/lib/warning';
import { supportRef, useComposeRef } from 'rc-util/lib/ref';
/**
 * Internal dependencies
 */
import OrderContext from './Context';
import useDom from './useDom';
import useScrollLocker from './useScrollLocker';
import { inlineMock } from './mock';
const getPortalContainer = ( getContainer ) => {
	if ( getContainer === false ) {
		return false;
	}
	if ( ! canUseDom() || ! getContainer ) {
		return null;
	}
	if ( typeof getContainer === 'string' ) {
		return document.querySelector( getContainer );
	}
	if ( typeof getContainer === 'function' ) {
		return getContainer();
	}
	return getContainer;
};
const Portal = React.forwardRef( ( props, ref ) => {
	const { open, autoLock, getContainer, debug, autoDestroy = true, children } = props;
	const [ shouldRender, setShouldRender ] = React.useState( open );
	const mergedRender = shouldRender || open;
	// ====================== Should Render ======================
	React.useEffect( () => {
		if ( autoDestroy || open ) {
			setShouldRender( open );
		}
	}, [ open, autoDestroy ] );
	// ======================== Container ========================
	const [ innerContainer, setInnerContainer ] = React.useState( () =>
		getPortalContainer( getContainer )
	);
	React.useEffect( () => {
		const customizeContainer = getPortalContainer( getContainer );
		// Tell component that we check this in effect which is safe to be `null`
		setInnerContainer( customizeContainer ?? null );
	} );
	const [ defaultContainer, queueCreate ] = useDom( mergedRender && ! innerContainer, debug );
	const mergedContainer = innerContainer ?? defaultContainer;
	// ========================= Locker ==========================
	useScrollLocker(
		autoLock &&
			open &&
			canUseDom() &&
			( mergedContainer === defaultContainer || mergedContainer === document.body )
	);
	// =========================== Ref ===========================
	let childRef = null;
	if ( children && supportRef( children ) && ref ) {
		( { ref: childRef } = children );
	}
	const mergedRef = useComposeRef( childRef, ref );
	// ========================= Render ==========================
	// Do not render when nothing need render
	// When innerContainer is `undefined`, it may not ready since user use ref in the same render
	if ( ! mergedRender || ! canUseDom() || innerContainer === undefined ) {
		return null;
	}
	// Render inline
	const renderInline = mergedContainer === false || inlineMock();
	let reffedChildren = children;
	if ( ref ) {
		reffedChildren = React.cloneElement( children, {
			ref: mergedRef,
		} );
	}
	return (
		<OrderContext.Provider value={ queueCreate }>
			{ renderInline ? reffedChildren : createPortal( reffedChildren, mergedContainer ) }
		</OrderContext.Provider>
	);
} );
export default Portal;
