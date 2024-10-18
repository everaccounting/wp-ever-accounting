/**
 * WordPress dependencies
 */
import { useEffect, useRef } from '@wordpress/element';

/**
 * Use something's value from the previous render.
 * Based on https://usehooks.com/usePrevious/.
 *
 * @param {*} value The value to store.
 *
 * @return {*} The previous value.
 */
export function usePreviousProps( value ) {
	const ref = useRef( {} );

	// Store current value in ref.
	useEffect( () => {
		if ( value !== undefined ) {
			ref.current = value;
		}
	}, [ value ] ); // Re-run when value changes.

	// Return previous value (happens before update in useEffect above).
	return ref.current;
}
