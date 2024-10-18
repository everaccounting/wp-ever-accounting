/**
 * WordPress dependencies
 */
import { useCallback, useEffect, useRef, useState } from '@wordpress/element';
import apiFetch from '@wordpress/api-fetch';
import { addQueryArgs } from '@wordpress/url';

/**
 * External dependencies
 */
import { isEmpty, isObject } from 'lodash';

export function useQueryResult( {
	endpoint,
	query: propsQuery = {},
	onChangeQuery: propsOnChangeQuery,
	cacheResult = false,
	...props
} ) {
	// === Refs ===
	const lastRequest = useRef( undefined );
	const mounted = useRef( false );

	// === State ===
	const [ stateQuery, setStateQuery ] = useState( isObject( propsQuery ) ? propsQuery : {} );
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
		( query, callback ) => {
			if ( ! endpoint ) {
				return callback();
			}
			const loader = apiFetch( { path: addQueryArgs( endpoint, query ) } );
			if ( loader && typeof loader.then === 'function' ) {
				loader.then( callback, () => callback() );
			}
		},
		[ endpoint ]
	);

	useEffect( () => {
		console.log("=== useQueryResult ===");
		fetchResult( stateQuery, ( result ) => {
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
		( query ) => {
			if ( propsOnChangeQuery ) {
				const newQuery = propsOnChangeQuery( query );
				if ( isObject( newQuery ) ) {
					query = newQuery;
				}
			}
			if ( isEmpty( query ) ) {
				lastRequest.current = undefined;
				setStateQuery( {} );
				setLoadedQuery( {} );
				setLoadedResult( [] );
				setIsLoading( false );
			}
			if ( cacheResult && resultCache[ JSON.stringify( query ) ] ) {
				lastRequest.current = undefined;
				setStateQuery( query );
				setLoadedQuery( query );
				setLoadedResult( resultCache[ JSON.stringify( query ) ] );
				setIsLoading( false );
			} else {
				const request = ( lastRequest.current = {} );
				setStateQuery( query );
				setIsLoading( true );
				fetchResult( query, ( result ) => {
					if ( ! mounted.current ) {
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
			}
		},
		[ cacheResult, fetchResult, propsOnChangeQuery, resultCache ]
	);

	return {
		query: stateQuery,
		loadedQuery,
		result: loadedResult,
		isLoading,
		onChangeQuery,
		...props,
	};
}
