/**
 * External dependencies
 */
import { useCallback, useEffect, useRef, useState } from 'react';
/**
 * Internal dependencies
 */
import { handleInputChange } from './utils';

export default function useAsync( {
	defaultOptions: propsDefaultOptions = false,
	cacheOptions = false,
	loadOptions: propsLoadOptions,
	options: propsOptions,
	isLoading: propsIsLoading = false,
	onInputChange: propsOnInputChange,
	filterOption = null,
	...restSelectProps
} ) {
	const { inputValue: propsInputValue } = restSelectProps;

	const lastRequest = useRef( undefined );
	const mounted = useRef( false );

	const [ defaultOptions, setDefaultOptions ] = useState(
		Array.isArray( propsDefaultOptions ) ? propsDefaultOptions : undefined
	);
	const [ stateInputValue, setStateInputValue ] = useState(
		typeof propsInputValue !== 'undefined' ? propsInputValue : ''
	);
	const [ isLoading, setIsLoading ] = useState( propsDefaultOptions === true );
	const [ loadedInputValue, setLoadedInputValue ] = useState( undefined );
	const [ loadedOptions, setLoadedOptions ] = useState( [] );
	const [ passEmptyOptions, setPassEmptyOptions ] = useState( false );
	const [ optionsCache, setOptionsCache ] = useState( {} );
	const [ prevDefaultOptions, setPrevDefaultOptions ] = useState( undefined );
	const [ prevCacheOptions, setPrevCacheOptions ] = useState( undefined );

	if ( cacheOptions !== prevCacheOptions ) {
		setOptionsCache( {} );
		setPrevCacheOptions( cacheOptions );
	}

	if ( propsDefaultOptions !== prevDefaultOptions ) {
		setDefaultOptions( Array.isArray( propsDefaultOptions ) ? propsDefaultOptions : undefined );
		setPrevDefaultOptions( propsDefaultOptions );
	}

	useEffect( () => {
		mounted.current = true;
		return () => {
			mounted.current = false;
		};
	}, [] );

	const loadOptions = useCallback(
		( inputValue, callback ) => {
			if ( ! propsLoadOptions ) {
				return callback();
			}
			const loader = propsLoadOptions( inputValue, callback );
			if ( loader && typeof loader.then === 'function' ) {
				loader.then( callback, () => callback() );
			}
		},
		[ propsLoadOptions ]
	);

	useEffect( () => {
		if ( propsDefaultOptions === true ) {
			loadOptions( stateInputValue, ( options ) => {
				if ( ! mounted.current ) {
					return;
				}
				setDefaultOptions( options || [] );
				setIsLoading( !! lastRequest.current );
			} );
		}
		// NOTE: this effect is designed to only run when the component mounts,
		// so we don't want to include any hook dependencies
		// eslint-disable-next-line react-hooks/exhaustive-deps
	}, [] );

	const onInputChange = useCallback(
		( newValue, actionMeta ) => {
			const inputValue = handleInputChange( newValue, actionMeta, propsOnInputChange );
			if ( ! inputValue ) {
				lastRequest.current = undefined;
				setStateInputValue( '' );
				setLoadedInputValue( '' );
				setLoadedOptions( [] );
				setIsLoading( false );
				setPassEmptyOptions( false );
				return;
			}
			if ( cacheOptions && optionsCache[ inputValue ] ) {
				setStateInputValue( inputValue );
				setLoadedInputValue( inputValue );
				setLoadedOptions( optionsCache[ inputValue ] );
				setIsLoading( false );
				setPassEmptyOptions( false );
			} else {
				const request = ( lastRequest.current = {} );
				setStateInputValue( inputValue );
				setIsLoading( true );
				setPassEmptyOptions( ! loadedInputValue );
				loadOptions( inputValue, ( options ) => {
					if ( ! mounted ) {
						return;
					}
					if ( request !== lastRequest.current ) {
						return;
					}
					lastRequest.current = undefined;
					setIsLoading( false );
					setLoadedInputValue( inputValue );
					setLoadedOptions( options || [] );
					setPassEmptyOptions( false );
					setOptionsCache(
						options ? { ...optionsCache, [ inputValue ]: options } : optionsCache
					);
				} );
			}
		},
		[ cacheOptions, loadOptions, loadedInputValue, optionsCache, propsOnInputChange ]
	);

	const options = passEmptyOptions
		? []
		: stateInputValue && loadedInputValue
		? loadedOptions
		: defaultOptions || [];

	return {
		...restSelectProps,
		options,
		isLoading: isLoading || propsIsLoading,
		onInputChange,
		filterOption,
	};
}
