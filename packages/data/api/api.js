/**
 * WordPress dependencies
 */
import apiFetch from '@wordpress/api-fetch';
import { addQueryArgs } from '@wordpress/url';

export const api = ( options ) => {
	return new Promise( ( resolve, reject ) => {
		apiFetch( { ...options, parse: false } )
			.then( ( response ) => {
				response.json().then( ( data ) => {
					resolve( {
						data,
						...( response.headers.has( 'X-WP-Total' )
							? {
									total: parseInt(
										response.headers.get( 'X-WP-Total' ),
										10
									),
							  }
							: {} ),
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

export default {
	request: api,
	get: ( path, args ) => {
		return api( {
			path: addQueryArgs( path, args ),
			method: 'GET',
		} );
	},
	post: ( path, args ) => {
		return api( {
			path,
			method: 'POST',
			data: args,
		} );
	},
	put: ( path, args ) => {
		return api( {
			path,
			method: 'PUT',
			data: args,
		} );
	},
	delete: ( path, args ) => {
		return api( {
			path,
			method: 'DELETE',
			data: args,
		} );
	},
};
