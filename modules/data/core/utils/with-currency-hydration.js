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
export const withCurrencyHydration = ( currencies ) =>
	createHigherOrderComponent(
		( OriginalComponent ) => ( props ) => {
			const userRef = useRef( currencies );
			useSelect( ( select, registry ) => {
				if ( ! userRef.current ) {
				}
				const { isResolving, hasFinishedResolution } = select(
					STORE_NAME
				);
				const {
					startResolution,
					finishResolution,
					receiveEntityRecords,
				} = registry.dispatch( STORE_NAME );
				if (
					! isResolving( 'getEntityRecords', [ 'currencies' ] ) &&
					! hasFinishedResolution( 'getEntityRecords', [
						'currencies',
					] )
				) {
					startResolution( 'getEntityRecords', [ 'currencies' ] );
					receiveEntityRecords(
						'currencies',
						currencies,
						{ per_page: -1 },
						'code'
					);
					finishResolution( 'getEntityRecords', [ 'currencies' ] );
				}
			} );

			return <OriginalComponent { ...props } />;
		},
		'withCurrencyHydration'
	);
