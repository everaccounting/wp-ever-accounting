/**
 * WordPress dependencies
 */
import { addQueryArgs } from '@wordpress/url';
import apiFetch from '@wordpress/api-fetch';

/**
 * Internal dependencies
 */
import { getNormalizedCommaSeparable } from './utils';
import { forwardResolver } from '../utils';
import { DEFAULT_KEY } from './constants';

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
							entity.key || DEFAULT_KEY,
						] ),
					].join(),
				};
			}

			const path = addQueryArgs( entity.baseURL + ( key ? '/' + key : '' ), {
				...entity.baseURLParams,
				...query,
			} );

			const record = await apiFetch( { path } );

			if ( query._fields ) {
				query._fields.split( ',' ).forEach( ( field ) => {
					if ( ! record.hasOwnProperty( field ) ) {
						record[ field ] = undefined;
					}
				} );
			}

			await dispatch.receiveRecords( name, record, query );
		} catch ( error ) {
			console.log( error );
			dispatch.receiveQueryError( name, error, { ...query, key } );
			throw error;
		}
	};

/**
 * Requests an entity's record from the REST API.
 */
export const getRawRecord = forwardResolver( 'getRecord' );

/**
 * Requests an entity's record from the REST API.
 */
export const getEditedRecord = forwardResolver( 'getRecord' );

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
			const entity = await select.getEntity( name );
			if ( ! entity || entity.name !== name ) {
				new Error( `Entity "${ name }" does not exist.` );
			}
			if ( query._fields ) {
				// If requesting specific fields, items and query association to said
				// records are stored by ID reference. Thus, fields must always include
				// the ID.
				query = {
					...query,
					_fields: [
						...new Set( [
							...( getNormalizedCommaSeparable( query._fields ) || [] ),
							entity.key || DEFAULT_KEY,
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
			await dispatch.receiveRecords( name, records, query );
			await dispatch.receiveRecordsCount( name, count, query );

			// When requesting all fields, the list of results can be used to
			// resolve the `getEntityRecord` selector in addition to `getRecords`.
			// See https://github.com/WordPress/gutenberg/pull/26575
			if ( ! query?._fields && ! query.context ) {
				const key = entity.key || DEFAULT_KEY;
				const resolutionsArgs = records
					.filter( ( record ) => record[ key ] )
					.map( ( record ) => [ name, record[ key ] ] );
				await dispatch( {
					type: 'START_RESOLUTIONS',
					selectorName: 'getRecord',
					args: resolutionsArgs,
				} );
				await dispatch( {
					type: 'FINISH_RESOLUTIONS',
					selectorName: 'getRecord',
					args: resolutionsArgs,
				} );
			}
		} catch ( error ) {
			console.log( error );
			dispatch.receiveQueryError( name, error, query );
			throw error;
		}
	};

getRecords.shouldInvalidate = ( action, name ) => {
	return (
		( action.type === 'RECEIVE_RECORDS' || action.type === 'REMOVE_RECORDS' ) &&
		action.name === name &&
		action.invalidateCache
	);
};

/**
 * Returns the Entity's records.
 *
 * @param {Object}  state    State tree
 * @param {string}  name     Entity name.
 * @param {?Object} query    Optional terms query.
 *
 * @param {Array}   defaults Default value.
 * @return {number} Record Count.
 */
export const getRecordsCount = forwardResolver( 'getRecords' );
