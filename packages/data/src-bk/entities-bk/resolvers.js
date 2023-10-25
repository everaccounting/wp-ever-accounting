/**
 * WordPress dependencies
 */
import { addQueryArgs } from '@wordpress/url';
import apiFetch from '@wordpress/api-fetch';

/**
 * Internal dependencies
 */
import { getNormalizedCommaSeparable } from './utils';
import { DEFAULT_PRIMARY_KEY } from './constants.js';

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
		const entity = await select.getEntity( name );
		if ( ! entity ) {
			return Promise.reject( `Could not find any entity named "${ name }" please check entity config` );
		}

		try {
			if ( query._fields ) {
				// If requesting specific fields, items and query association to said
				// records are stored by ID reference. Thus, fields must always include
				// the ID.
				query = {
					...query,
					_fields: [ ...new Set( [ ...( getNormalizedCommaSeparable( query._fields ) || [] ), entity.primaryKey || DEFAULT_PRIMARY_KEY ] ) ].join(),
				};
			}

			const path = addQueryArgs( entity.baseURL, {
				...entity.baseURLParams,
				...query,
			} );
			const response = await apiFetch( { path, parse: false } );
			let records = await response.json();
			const count = parseInt( response.headers.get( 'x-wp-total' ), 10 );

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
			dispatch( {
				type: 'RECEIVE_RECORDS',
				records: Array.isArray( records ) ? records : [ records ],
				name,
				query,
				primaryKey: entity.primaryKey || DEFAULT_PRIMARY_KEY,
			} );
			dispatch( {
				type: 'RECEIVE_RECORDS_COUNT',
				count,
				name,
				query,
			} );
			// When requesting all fields, the list of results can be used to
			// resolve the `getEntityRecord` selector in addition to `getEntityRecords`.
			// See https://github.com/WordPress/gutenberg/pull/26575
			if ( ! query?._fields ) {
				const key = entity.key || DEFAULT_PRIMARY_KEY;
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
			dispatch( {
				type: 'RECEIVE_RECORDS_ERROR',
				name,
				query,
				error,
			} );
			dispatch( {
				type: 'RECEIVE_RECORDS_COUNT_ERROR',
				name,
				query,
				error,
			} );
			throw error;
		}
	};
