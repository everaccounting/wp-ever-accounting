/**
 * WordPress dependencies
 */
import apiFetch from '@wordpress/api-fetch';
import { addQueryArgs } from '@wordpress/url';

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

export const postRequest = ( url, data = {} ) => {
	return request( {
		method: 'POST',
		path: url,
		data,
	} );
};

export const putRequest = ( url, data = {} ) => {
	return request( {
		method: 'PUT',
		path: url,
		data,
	} );
};

export const deleteRequest = ( url, data = {} ) => {
	return request( {
		method: 'DELETE',
		path: url,
		data,
	} );
};
