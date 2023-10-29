/**
 * WordPress dependencies
 */
import { useState, useCallback } from '@wordpress/element';
/**
 * External dependencies
 */
import { isNil } from 'lodash';

/**
 * Simplified and improved implementation of useControlledState.
 *
 * @param {Object}   props
 * @param {*}        [props.defaultValue] An initial state value.
 * @param {*}        [props.value]
 * @param {Function} [props.onChange]
 * @return {Array} [value, setValue] A stateful value and a function to update it.
 */
export function useMergedState( { defaultValue, onChange, value: valueProp } ) {
	const hasValue = ! isNil( valueProp );
	const initialValue = hasValue ? valueProp : defaultValue;

	const [ state, setState ] = useState( initialValue );

	const value = hasValue ? valueProp : state;
	const setValue = hasValue && ! isNil( onChange ) ? onChange : setState;

	return [ value, setValue ];
}

export default useMergedState;

/**
 * A enhanced hook for managing and coordinating internal state and
 * state from props. This hook is useful for creating controlled components,
 * such as a custom <Input />.
 *
 * @param {any} initialValue An initial state value.
 *
 * @param       currentState
 * @param       options
 * @example
 * const [value, setValue] = useControlledState(valueFromProps);
 */
// export function useControlledState( currentState, options ) {
// 	const { initial } = { initial: undefined, ...options };
// 	const [ internalState, setInternalState ] = useState( initial );
// 	const hasCurrentState = currentState !== undefined;
//
// 	const setState = useCallback(
// 		( nextState ) => {
// 			if ( ! hasCurrentState ) {
// 				setInternalState( nextState );
// 			}
// 		},
// 		[ hasCurrentState ]
// 	);
//
// 	const state = hasCurrentState ? currentState : internalState;
//
// 	return [ state, setState ];
// }
