/**
 * WordPress dependencies
 */
import { useState, useCallback, useRef } from '@wordpress/element';

export function useControlledState( { current, default: defaultProp, fallback = '' } ) {
	const { current: isControlled } = useRef( current !== undefined );
	const [ valueState, setValue ] = useState( defaultProp || fallback );
	const value = isControlled ? current : valueState;

	const setValueIfUncontrolled = useCallback(
		( newValue ) => {
			if ( ! isControlled ) {
				setValue( newValue );
			}
		},
		[ isControlled ]
	);

	return [ value, setValueIfUncontrolled ];
}
