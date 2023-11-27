/**
 * WordPress dependencies
 */
import { useState } from '@wordpress/element';

/**
 * External dependencies
 */
import { noop } from 'lodash';

function useQuery( props, onChange = noop ) {
	const hasQuery = typeof props?.query !== 'undefined';
	const initialQuery = hasQuery ? props.query : {};
	const [ state, setState ] = useState( initialQuery );
	const value = hasQuery ? props.query : state;
	// remove defaultQuery from query.
	let setValue;
	if ( hasQuery && typeof onChange === 'function' ) {
		setValue = onChange;
	} else if ( ! hasQuery && typeof onChange === 'function' ) {
		setValue = ( nextValue ) => {
			onChange( nextValue );
			setState( nextValue );
		};
	} else {
		setValue = setState;
	}

	return [ value, setValue ];
}

export default useQuery;
