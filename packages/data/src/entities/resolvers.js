/**
 * WordPress dependencies
 */
import { addQueryArgs } from '@wordpress/url';
import apiFetch from '@wordpress/api-fetch';

/**
 * Internal dependencies
 */
import { getNormalizedCommaSeparable } from './utils';
import { DEFAULT_KEY } from './constants';

/**
 * Requests the entity's records from the REST API.
 *
 * @param {string}  name  Entity name.
 * @param {Object?} query Query Object. If requesting specific fields, fields
 *                        must always include the ID.
 */
export const getRecords =
	( name, query = {} ) =>
	async ( { select, dispatch } ) => {
		try {
			const entity = await select.getConfig( name );
			if ( ! entity || entity.name !== name ) {
				new Error( `Entity "${ name }" does not exist.` );
			}
			if ( query._fields ) {
				// If requesting specific fields, items and query association to said
				// records are stored by ID reference. Thus, fields must always include
				// the ID.
				query = {
					...query,
					_fields: [ ...new Set( [ ...( getNormalizedCommaSeparable( query._fields ) || [] ), entity.key || DEFAULT_KEY ] ) ].join(),
				};
			}
			const path = addQueryArgs( entity.baseURL, {
				...entity.baseURLParams,
				...query,
			} );
			const response = await apiFetch( { path, parse: false } );
			let records = await response.json();
			const count = parseInt( response.headers.get( 'x-wp-total' ) || '', 10 );
			if ( query._fields ) {
				records = records.map( ( record ) => {
					query._fields.split( ',' ).forEach( ( field ) => {
						if ( ! record.hasOwnProperty( field ) ) {
							record[ field ] = undefined;
						}
					} );

					return record;
				} );
			}
			dispatch.receiveRecords( name, records, query );
			dispatch.receiveRecordsCount( name, count, query );

			// When requesting all fields, the list of results can be used to
			// resolve the `getEntityRecord` selector in addition to `getRecords`.
			// See https://github.com/WordPress/gutenberg/pull/26575
			if ( ! query?._fields && ! query.context ) {
				const key = entity.key || DEFAULT_KEY;
				const resolutionsArgs = records.filter( ( record ) => record[ key ] ).map( ( record ) => [ name, record[ key ] ] );
				dispatch( {
					type: 'START_RESOLUTIONS',
					selectorName: 'getRecord',
					args: resolutionsArgs,
				} );
				dispatch( {
					type: 'FINISH_RESOLUTIONS',
					selectorName: 'getRecord',
					args: resolutionsArgs,
				} );
			}
		} catch ( error ) {
			console.log( error );
			throw error;
		}
	};

getRecords.shouldInvalidate = ( action, name ) => {
	return ( action.type === 'RECEIVE_RECORDS' || action.type === 'REMOVE_RECORDS' ) && action.name === name && action.invalidateCache;
};
