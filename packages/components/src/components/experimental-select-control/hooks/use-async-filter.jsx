/**
 * External dependencies
 */
/**
 * WordPress dependencies
 */
import { Spinner } from '@wordpress/components';
import { useDebounce } from '@wordpress/compose';
import { useCallback, useState } from '@wordpress/element';
/**
 * Internal dependencies
 */
import { SuffixIcon } from '../suffix-icon';
export const DEFAULT_DEBOUNCE_TIME = 250;
export default function useAsyncFilter( {
	filter,
	onFilterStart,
	onFilterEnd,
	onFilterError,
	debounceTime,
} ) {
	const [ isFetching, setIsFetching ] = useState( false );
	const handleInputChange = useCallback(
		function handleInputChangeCallback( value ) {
			if ( typeof filter === 'function' ) {
				if ( typeof onFilterStart === 'function' ) onFilterStart( value );
				setIsFetching( true );
				filter( value )
					.then( ( filteredItems ) => {
						if ( typeof onFilterEnd === 'function' )
							onFilterEnd( filteredItems, value );
					} )
					.catch( ( error ) => {
						if ( typeof onFilterError === 'function' ) onFilterError( error, value );
					} )
					.finally( () => {
						setIsFetching( false );
					} );
			}
		},
		[ filter, onFilterStart, onFilterEnd, onFilterError ]
	);
	return {
		isFetching,
		suffix: isFetching === true ? <SuffixIcon icon={ <Spinner /> } /> : undefined,
		getFilteredItems: ( items ) => items,
		onInputChange: useDebounce(
			handleInputChange,
			typeof debounceTime === 'number' ? debounceTime : DEFAULT_DEBOUNCE_TIME
		),
	};
}
