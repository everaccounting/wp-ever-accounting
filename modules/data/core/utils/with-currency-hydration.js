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
				currencies.forEach( ( currency ) => {
					const code = currency.code;
					if (
						! isResolving( 'getEntityRecord', [
							'currencies',
							code,
						] ) &&
						! hasFinishedResolution( 'getEntityRecord', [
							'currencies',
							code,
						] )
					) {
						startResolution( 'getEntityRecord', [
							'currencies',
							code,
						] );
						receiveEntityRecords(
							'currencies',
							currency,
							{},
							'code'
						);
						finishResolution( 'getEntityRecord', [
							'currencies',
							code,
						] );
					}
				} );
			} );

			return <OriginalComponent { ...props } />;
		},
		'withCurrencyHydration'
	);
