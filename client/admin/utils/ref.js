/**
 * WordPress dependencies
 */
import { useMemo } from '@wordpress/element';

export function fillRef( ref, node ) {
	if ( typeof ref === 'function' ) {
		ref( node );
	} else if ( typeof ref === 'object' && ref && 'current' in ref ) {
		ref.current = node;
	}
}

/**
 * Merge refs into one ref function to support ref passing.
 * @param {...any} refs
 */
export function composeRef( ...refs ) {
	const refList = refs.filter( ( ref ) => ref );
	if ( refList.length <= 1 ) {
		return refList[ 0 ];
	}
	return ( node ) => {
		refs.forEach( ( ref ) => {
			fillRef( ref, node );
		} );
	};
}

export function useComposeRef( ...refs ) {
	return useMemo(
		() => composeRef( ...refs ),
		[ refs ],
		( prev, next ) => prev.length !== next.length || prev.every( ( ref, i ) => ref !== next[ i ] )
	);
}
