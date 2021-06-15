/**
 * External dependencies
 */
import { createHigherOrderComponent } from '@wordpress/compose';
import { useSelect } from '@wordpress/data';

/**
 * Internal dependencies
 */
import { STORE_NAME } from './constants';

/**
 * Higher-order component used to hydrate current user data.
 **/
export const withSettings = () =>
	createHigherOrderComponent(
		(OriginalComponent) => (props) => {
			useSelect((select, registry) => {

				const {isResolving} = select(
					STORE_NAME
				);

				const {
					startResolution,
					finishResolution,
				} = registry.dispatch(STORE_NAME);

				if (
					!isResolving('getOptions')
				) {
					startResolution('getOptions');
					finishResolution('getOptions');
				}
			});

			return <OriginalComponent {...props} />;
		},
		'withSettings'
	);
