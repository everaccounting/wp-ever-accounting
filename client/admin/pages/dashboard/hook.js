/**
 * External dependencies
 */
import { isNil } from 'lodash';
import { useState, useCallback } from 'react';

/**
 * Simplified and improved implementation of useControlledState.
 *
 * @template T
 * @param {Object}             props
 * @param {T}                  [props.defaultValue]
 * @param {T}                  [props.value]
 * @param {(value: T) => void} [props.onChange]
 * @return {[T|undefined, (value: T) => void]}
 */
export function useControlledValue( { defaultValue, onChange, value: valueProp } ) {
	const hasValue = ! isNil( valueProp );
	const initialValue = hasValue ? valueProp : defaultValue;

	const [ state, setState ] = useState( initialValue );

	const value = hasValue ? valueProp : state;
	const setValue = hasValue && ! isNil( onChange ) ? onChange : setState;

	return [ value, setValue ];
}

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
export function useControlledState( currentState, options ) {
	const { initial } = { initial: undefined, ...options };
	const [ internalState, setInternalState ] = useState( initial );
	const hasCurrentState = currentState !== undefined;

	const setState = useCallback(
		( nextState ) => {
			if ( ! hasCurrentState ) {
				setInternalState( nextState );
			}
		},
		[ hasCurrentState ]
	);

	const state = hasCurrentState ? currentState : internalState;

	return [ state, setState ];
}

/**
 * Similar to `useState` but will use props value if provided.
 * Note that internal use rc-util `useState` hook.
 */
export default function useMergedState(defaultStateValue, option) {
    const { onChange, value: valueProp } = option || {};
}
