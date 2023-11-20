/**
 * WordPress dependencies
 */
import { useEffect, useRef } from '@wordpress/element';

/**
 * Internal dependencies
 */
import { isValueDefined } from '../utils';

/**
 * The difference between this hook and the `usePrevious` hook is that this hook will only update the previous value
 * if the current value is truthy and different from the previous value. This is useful for cases where you want to use
 * the previous value to compare against the current value, but you don't want to update the previous value if the current
 * value is falsy.
 *
 * @param {*}        value          The initial value.
 * @param {Function} [shouldUpdate] An optional function that will be called with the previous and current values. If the current value is falsy, the previous value will not be updated.
 * @return {*} The previous value.
 */
const usePrevious = ( value, shouldUpdate = isValueDefined ) => {
	const previous = useRef( null );

	useEffect( () => {
		if ( shouldUpdate( value ) && value !== previous.current ) {
			previous.current = value;
		}
	}, [ shouldUpdate, value ] );

	return previous.current;
};

export default usePrevious;
