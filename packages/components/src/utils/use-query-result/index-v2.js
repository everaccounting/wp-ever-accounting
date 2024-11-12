/**
 * WordPress dependencies
 */
import { useCallback, useEffect, useRef, useState } from '@wordpress/element';

/**
 * External dependencies
 */
import { isEmpty, isObject } from 'lodash';

function handleInputChange( inputValue, actionMeta, onInputChange ) {
	if ( onInputChange ) {
		const newValue = onInputChange( inputValue, actionMeta );
		if ( typeof newValue === 'string' ) {
			return newValue;
		}
	}
	return inputValue;
}

export function useQueryResult( {
	result: propsResult,
	query: propsQuery,
	onChangeQuery: propsOnChangeQuery,
	fetchResult: propsFetchResult,
	cacheResult = false,
	...props
} ) {
	// === Refs ===
	const lastRequest = useRef( undefined );
	const mounted = useRef( false );

	// === State ===
	const [ query, setQuery ] = useState( isObject( propsQuery ) ? propsQuery : {} );
	const [ loadedQuery, setLoadedQuery ] = useState( undefined );
	const [ loadedResult, setLoadedResult ] = useState( [] );
	const [ isLoading, setIsLoading ] = useState( true );

	const [ resultCache, setResultCache ] = useState( {} );
	const [ prevCacheResult, setPrevCacheResult ] = useState( undefined );

	if ( cacheResult !== prevCacheResult ) {
		setResultCache( {} );
		setPrevCacheResult( cacheResult );
	}

	useEffect( () => {
		mounted.current = true;
		return () => {
			mounted.current = false;
		};
	}, [] );

	const fetchResult = useCallback(
		( _query, callback ) => {
			if ( ! propsFetchResult ) {
				return callback();
			}
			const loader = propsFetchResult( _query, callback );
			if ( loader && typeof loader.then === 'function' ) {
				loader.then( callback, () => callback() );
			}
		},
		[ propsFetchResult ]
	);

	useEffect( () => {
		fetchResult( query, ( result ) => {
			if ( ! mounted.current ) {
				return;
			}
			setLoadedResult( result || [] );
			setIsLoading( !! lastRequest.current );
		} );

		// NOTE: this effect is designed to only run when the component mounts,
		// so we don't want to include any hook dependencies
		// eslint-disable-next-line react-hooks/exhaustive-deps
	}, [] );

	const onChangeQuery = useCallback(
		( _query ) => {

			if ( isEmpty( _query ) ) {
				lastRequest.current = undefined;
				setQuery( {} );
				setLoadedQuery( {} );
				setLoadedResult( [] );
				setIsLoading( false );
				return;
			}

			if ( cacheResult && resultCache[ JSON.stringify( _query ) ] ) {
				setQuery( _query );
				setLoadedQuery( _query );
				setLoadedResult( resultCache[ JSON.stringify( _query ) ] );
				setIsLoading( false );
			} else {
				const request = ( lastRequest.current = {} );
				setQuery( _query );
				setIsLoading( true );
				fetchResult( _query, ( result ) => {
					if ( ! mounted ) {
						return;
					}
					if ( request !== lastRequest.current ) {
						return;
					}
					lastRequest.current = undefined;
					setIsLoading( false );
					setLoadedQuery( _query );
					setLoadedResult( result || [] );
					if ( cacheResult ) {
						setResultCache( { ...resultCache, [ JSON.stringify( _query ) ]: result } );
					}
				} );
			}
		},
		[ cacheResult, fetchResult, resultCache ]
	);

	const result = loadedResult || propsResult;

	return {
		query,
		result,
		isLoading,
		onChangeQuery,
		...props,
	};
}
