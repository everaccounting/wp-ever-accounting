/**
 * WordPress dependencies
 */
import { useCallback, useEffect, useRef, useState } from '@wordpress/element';

/**
 * External dependencies
 */
import { isEmpty, isObject } from 'lodash';

export default function useQueryResult( {
	result: propsResult,
	query: propsQuery,
	onChangeQuery: propsOnChangeQuery,
	fetchResult: propsFetchResult,
	cacheResult = false,
} ) {
	const lastRequest = useRef( undefined );
	const mounted = useRef( false );

	// === State ===
	const [ queryState, setQuery ] = useState( isObject( propsQuery ) ? propsQuery : {} );
	const [ prevQuery, setPrevQuery ] = useState( undefined );
	const [ isLoading, setIsLoading ] = useState( true );
	const [ loadedQuery, setLoadedQuery ] = useState( undefined );
	const [ loadedResult, setLoadedResult ] = useState( [] );
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
		( query, callback ) => {
			if ( ! propsFetchResult ) {
				return callback();
			}
			const loader = propsFetchResult( query, callback );
			if ( loader && typeof loader.then === 'function' ) {
				loader.then( callback, () => callback() );
			}
		},
		[ propsFetchResult ]
	);

	const onChangeQuery = useCallback(
		( query ) => {
			const request = ( lastRequest.current = {} );
			setQuery( query );
			setIsLoading( true );
			fetchResult( query, ( result ) => {
				if ( ! mounted ) {
					return;
				}
				if ( request !== lastRequest.current ) {
					return;
				}
				lastRequest.current = undefined;
				setIsLoading( false );
				setLoadedQuery( query );
				setLoadedResult( result || [] );
				if ( cacheResult ) {
					setResultCache( { ...resultCache, [ JSON.stringify( query ) ]: result } );
				}
			} );
		},
		[ propsOnChangeQuery ]
	);
}
