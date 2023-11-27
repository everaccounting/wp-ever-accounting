/**
 * WordPress dependencies
 */
import { useState } from '@wordpress/element';

/**
 * Simplified and improved implementation of useControlledState.
 *
 * @param {Object}   props              The props object.
 * @param {*}        props.defaultValue The default value.
 * @param {*}        props.value        The value.
 * @param {Function} props.onChange     The change handler.
 *
 * @return {Array} The controlled value and the value setter.
 */
export function useControlledValue({ defaultValue, onChange, value: valueProp }) {
	const hasValue = typeof valueProp !== 'undefined';
	const initialValue = hasValue ? valueProp : defaultValue;
	const [state, setState] = useState(initialValue);
	const value = hasValue ? valueProp : state;

	let setValue;
	if (hasValue && typeof onChange === 'function') {
		setValue = onChange;
	} else if (!hasValue && typeof onChange === 'function') {
		setValue = (nextValue) => {
			setState(nextValue);
			onChange(nextValue);
		};
	} else {
		setValue = setState;
	}

	return [value, setValue];
}

export default useControlledValue;
