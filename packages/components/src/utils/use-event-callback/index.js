/**
 * WordPress dependencies
 */
import { useEffect, useLayoutEffect, useRef } from '@wordpress/element';
/**
 * A version of `React.useLayoutEffect` that does not show a warning when server-side rendering.
 * This is useful for effects that are only needed for client-side rendering but not for SSR.
 *
 * Before you use this hook, make sure to read https://gist.github.com/gaearon/e7d97cdf38a2907924ea12e4ebdf3c85
 * and confirm it doesn't apply to your use-case.
 */
const useEnhancedEffect = typeof window !== 'undefined' ? useLayoutEffect : useEffect;

export function useEventCallback( fn ) {
	const ref = useRef( fn );
	useEnhancedEffect( () => {
		ref.current = fn;
	} );
	return useRef( function ( ...args ) {
		return ref.current.apply( this, args );
	} ).current;
}
