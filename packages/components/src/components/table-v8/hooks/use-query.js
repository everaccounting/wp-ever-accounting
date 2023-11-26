/**
 * WordPress dependencies
 */
import { useState } from '@wordpress/element';

function useQuery( props ) {
	const hasQuery = typeof props?.query !== 'undefined';
	const initialQuery = hasQuery ? props.query : {};
	const [ state, setState ] = useState( initialQuery );
	const query = hasQuery ? props.query : state;
	// remove defaultQuery from query.
	let setQuery;
	if ( hasQuery && typeof props?.onChange === 'function' ) {
		setQuery = props.onChange;
	} else if ( ! hasQuery && typeof onChange === 'function' ) {
		setQuery = ( nextValue ) => {
			props.onChange( nextValue );
			setState( nextValue );
		};
	} else {
		setQuery = setState;
	}

	return [ query, setQuery ];
}

export default useQuery;
