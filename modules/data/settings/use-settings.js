/**
 * External dependencies
 */
/**
 * WordPress dependencies
 */
import { useDispatch, useSelect } from '@wordpress/data';
import { useCallback } from '@wordpress/element';
/**
 * Internal dependencies
 */
import { STORE_NAME } from './constants';

/**
 * Hook for getting setting data.
 *
 * @param {Object} props properties.
 * @return {Object} Settings selectors and actions
 */
export const useSettings = ( props ) => {
	const { isRequesting, options, getOption } = useSelect(
		( select ) => {
			const { getOption, getOptions } = select( STORE_NAME );

			return {
				isRequesting: select( 'core/data' ).isResolving(
					STORE_NAME,
					'getOptions'
				),
				options: getOptions(),
				getOption,
			};
		},
		[ props ]
	);

	const {
		updateOption: saveOption,
		updateOptions: saveOptions,
	} = useDispatch( STORE_NAME );

	const updateOption = useCallback(
		( name, value ) => saveOption( name, value ),
		[ saveOption ]
	);

	const updateOptions = useCallback( ( options ) => saveOptions( options ), [
		saveOptions,
	] );

	const { default_currency = 'USD', default_account } = options;

	const { defaultCurrency, defaultAccount = {} } = useSelect(
		( select ) => {
			const { getEntityRecord } = select( STORE_NAME );
			return {
				defaultCurrency: getEntityRecord(
					'currency',
					default_currency
				),
				defaultAccount: getEntityRecord( 'account', default_account ),
			};
		},
		[ default_currency, default_account ]
	);

	return {
		...options,
		getOption,
		updateOption,
		updateOptions,
		isRequesting,
		defaultCurrency,
		defaultAccount,
	};
};
