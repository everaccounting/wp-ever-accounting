/**
 * WordPress dependencies
 */
import { createHigherOrderComponent } from '@wordpress/compose';
import { useSelect } from '@wordpress/data';
import { useRef } from '@wordpress/element';

/**
 * Internal dependencies
 */
import { STORE_NAME } from '../constants';

/**
 * Higher-order component used to hydrate current user data.
 *
 */
export const withCurrentUserHydration = ( currentUser ) =>
	createHigherOrderComponent(
		( OriginalComponent ) => ( props ) => {
			const userRef = useRef( currentUser );

			useSelect( ( select, registry ) => {
				if ( ! userRef.current ) {
				}

				const { isResolving, hasFinishedResolution } = select(
					STORE_NAME
				);
				const {
					startResolution,
					finishResolution,
					receiveCurrentUser,
				} = registry.dispatch( STORE_NAME );
				if (
					! isResolving( 'getCurrentUser' ) &&
					! hasFinishedResolution( 'getCurrentUser' )
				) {
					startResolution( 'getCurrentUser', [] );
					receiveCurrentUser( userRef.current );
					finishResolution( 'getCurrentUser', [] );
				}
			} );

			return <OriginalComponent { ...props } />;
		},
		'withCurrentUserHydration'
	);
