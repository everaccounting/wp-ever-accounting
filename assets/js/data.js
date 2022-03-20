/**
 * WordPress dependencies
 */
import { useRef, useCallback, useEffect, useState } from '@wordpress/element';
import apiFetch from '@wordpress/api-fetch';
import { addQueryArgs } from '@wordpress/url';
/**
 * External dependencies
 */
import { isEqual } from 'lodash';

export const request = ( options ) => {
	return new Promise( ( resolve, reject ) => {
		apiFetch( { ...options, parse: false } )
			.then( ( response ) => {
				response.json().then( ( data ) => {
					resolve( {
						data,
						headers: response.headers,
					} );
				} );
			} )
			.catch( ( error ) => {
				error.json().then( ( json ) => {
					reject( json );
				} );
			} );
	} );
};

export const getRequest = ( url, params = {} ) => {
	return request( {
		method: 'GET',
		path: addQueryArgs( url, params ),
	} );
};

const useQuery = ( url, args = {}, options = {} ) => {
	const [ state, mergeState ] = useState( {
		data: true,
		error: null,
		isRequesting: null,
		args: {},
	} );

	const makeRequest = useCallback( () => {
		getRequest( url, args ).then(
			( { data, total } ) => {
				mergeState( {
					data,
					...( total ? { total } : {} ),
					error: null,
					isRequesting: false,
				} );
			},
			( error ) => {
				mergeState(
					state,
					...{ error, data: null, isRequesting: false }
				);
			}
		);
	}, [ url, args ] );

	return [
		{
			...state,
			// setLocalData,
		},
		makeRequest(),
	];
};

export default useQuery;
