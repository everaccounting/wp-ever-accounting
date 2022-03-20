/**
 * WordPress dependencies
 */
import { useState, useCallback, useRef } from '@wordpress/element';
/**
 * External dependencies
 */
import { isFunction, isEqual } from 'lodash';

export const useMergeState = ( initialState ) => {
	const [ state, setState ] = useState( initialState || {} );

	const mergeState = useCallback( ( newState ) => {
		if ( isFunction( newState ) ) {
			setState( ( currentState ) => ( {
				...currentState,
				...newState( currentState ),
			} ) );
		} else {
			setState( ( currentState ) => ( {
				...currentState,
				...newState,
			} ) );
		}
	}, [] );

	return [ state, mergeState ];
};

export const useDeepCompareMemoize = ( value ) => {
	const valueRef = useRef();

	if ( ! isEqual( value, valueRef.current ) ) {
		valueRef.current = value;
	}
	return valueRef.current;
};
