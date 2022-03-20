/**
 * External dependencies
 */
/**
 * WordPress dependencies
 */
import { useCallback } from '@wordpress/element';

/**
 * Internal dependencies
 */
import { request } from './api';
import { useMergeState } from './utils';

const useMutation = ( method, url ) => {
	const [ state, mergeState ] = useMergeState( {
		data: null,
		error: null,
		isWorking: false,
	} );

	const makeRequest = useCallback(
		( variables = {} ) =>
			new Promise( ( resolve, reject ) => {
				mergeState( { isWorking: true } );

				request[ method ]( url, variables ).then(
					( data ) => {
						resolve( data );
						mergeState( { data, error: null, isWorking: false } );
					},
					( error ) => {
						reject( error );
						mergeState( { error, data: null, isWorking: false } );
					}
				);
			} ),
		[ method, url, mergeState ]
	);

	return [
		{
			...state,
			[ isWorkingAlias[ method ] ]: state.isWorking,
		},
		makeRequest,
	];
};

const isWorkingAlias = {
	post: 'isCreating',
	put: 'isUpdating',
	patch: 'isUpdating',
	delete: 'isDeleting',
};

export default useMutation;
