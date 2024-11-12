/**
 * WordPress dependencies
 */
import { useCallback, useEffect, useRef, useState } from '@wordpress/element';

export default function useAsyncQuery( {
	query: propsQuery,
	preload = false,
	cacheResult = false,
} ) {
	const lastRequest = useRef( undefined );
	const mounted = useRef( false );

	const [ stateQuery, setStateQuery ] = useState(
		typeof propsQuery !== 'undefined' ? propsQuery : {}
	);
	const [isLoading, setIsLoading] = useState(propsDefaultOptions === true);
}
