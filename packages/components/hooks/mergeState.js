/**
 * External dependencies
 */
import { isFunction } from 'lodash';
/**
 * WordPress dependencies
 */
import { useState, useCallback } from '@wordpress/element';

const useMergeState = ( initialState ) => {
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

export default useMergeState;
