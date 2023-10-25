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
import { receiveRecords, receiveRecordsCount } from './actions';

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
		}

		try {
			if ( query._fields ) {
				// If requesting specific fields, items and query association to said
				// records are stored by ID reference. Thus, fields must always include
				// the ID.
				query = {
					...query,
					_fields: [
						...new Set( [
							...( getNormalizedCommaSeparable( query._fields ) || [] ),
							entity.key || DEFAULT_PRIMARY_KEY,
						] ),
					].join(),
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

			receiveRecords( name, records, query );
			receiveRecordsCount( name, count, query );
			// When requesting all fields, the list of results can be used to
			// resolve the `getEntityRecord` selector in addition to `getEntityRecords`.
			// See https://github.com/WordPress/gutenberg/pull/26575
			if ( ! query?._fields ) {
				const key = entity.key || DEFAULT_PRIMARY_KEY;
				const resolutionsArgs = records
					.filter( ( record ) => record[ key ] )
					.map( ( record ) => [ name, record[ key ] ] );

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

/**
 * Requests an entity's record from the REST API.
 *
 * @param {string}           name  Entity name.
 * @param {number|string}    key   Record's key
 * @param {Object|undefined} query Optional object of query parameters to
 *                                 include with request. If requesting specific
 *                                 fields, fields must always include the ID.
 */
export const getRecord =
	( name, key, query = {} ) =>
	async ( { select, dispatch } ) => {
		const entity = await select.getEntity( name );
		if ( ! entity ) {
		}

		try {
			if ( query._fields ) {
				// If requesting specific fields, items and query association to said
				// records are stored by ID reference. Thus, fields must always include
				// the ID.
				query = {
					...query,
					_fields: [
						...new Set( [
							...( getNormalizedCommaSeparable( query._fields ) || [] ),
							entity.key || DEFAULT_PRIMARY_KEY,
						] ),
					].join(),
				};
			}

			const path = addQueryArgs( entity.baseURL + ( key ? '/' + key : '' ), {
				...entity.baseURLParams,
				...query,
			} );
			const response = await apiFetch( { path, parse: false } );
			const record = await response.json();

			if ( query._fields ) {
				query._fields.split( ',' ).forEach( ( field ) => {
					if ( ! record.hasOwnProperty( field ) ) {
						record[ field ] = undefined;
					}
				} );
			}

			dispatch.receiveRecord( name, record, query );
		} catch ( error ) {
			dispatch( {
				type: 'GET_RECORD_ERROR',
				name,
				key,
				query,
				error,
			} );
			throw error;
		}
	};
