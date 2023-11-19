/**
 * External dependencies
 */
import { isEmpty } from 'lodash';

/**
 * WordPress dependencies
 */
import apiFetch from '@wordpress/api-fetch';
import { addQueryArgs } from '@wordpress/url';

/**
 * Internal dependencies
 */
import { DEFAULT_KEY } from './constants';

/**
 * Returns an action object used in adding new entities.
 *
 * @param {Array} entities Entities received.
 *
 * @return {Object} Action object.
 */
export function addEntities( entities ) {
	return {
		type: 'ADD_ENTITIES',
		entities: Array.isArray( entities ) ? entities : [ entities ],
	};
}

/**
 * Returns an action object used in signalling that entity records have been received.
 *
 * @param {string}       name            Name of the received entity.
 * @param {Array|Object} records         Records received.
 * @param {?Object}      query           Query Object.
 * @param {?boolean}     invalidateCache Should invalidate query caches.
 * @param {?Object}      edits           Edits to reset.
 * @return {Object} Action object.
 */
export function receiveRecords( name, records, query, invalidateCache = false, edits ) {
	return {
		type: 'RECEIVE_RECORDS',
		records: Array.isArray( records ) ? records : [ records ],
		name,
		query,
		invalidateCache,
		edits,
	};
}

/**
 * Returns an action object used in signalling that entity records count have been received.
 *
 * @param { string } name            Name of the received entity.
 * @param { number } count           Count of records received.
 * @param {Object}   query           Query Object.
 * @param {boolean}  invalidateCache Should invalidate query caches.
 * @return {{total: number, invalidateCache: boolean, query: ?Object, name, type: string}} Receive Total.
 */
export function receiveRecordsCount( name, count, query = {}, invalidateCache = false ) {
	return {
		type: 'RECEIVE_RECORDS_COUNT',
		count,
		name,
		query,
		invalidateCache,
	};
}

/**
 * Returns an action object used in signalling that entity records have been
 * deleted, and they need to be removed from entities state.
 *
 * @param {string}              name            Name of the removed entities.
 * @param {Array|number|string} records         Record IDs of the removed entities.
 * @param {boolean}             invalidateCache Controls whether we want to invalidate the cache.
 * @return {Object} Action object.
 */
export function removeRecords( name, records, invalidateCache = false ) {
	return {
		type: 'REMOVE_RECORDS',
		recordIds: Array.isArray( records ) ? records : [ records ],
		name,
		invalidateCache,
	};
}

/**
 * Returns an action object used in signalling that errors have been received.
 *
 * @param {string}  name  Name of the entity.
 * @param {Object}  error Queried item total received.
 * @param {?Object} query Optional query object.
 *
 * @return {Object} Action object.
 */
export function receiveQueryError( name, error, query = {} ) {
	return {
		type: 'RECEIVE_QUERY_ERROR',
		name,
		error,
		query,
	};
}

/**
 * Action triggered to delete an entity record.
 *
 * @param {string}   name                   Name of the deleted entity.
 * @param {string}   recordId               Record ID of the deleted entity.
 * @param {?Object}  query                  Special query parameters for the
 *                                          DELETE API call.
 * @param {Object}   [options]              Delete options.
 * @param {Function} [options.fetchRequest] Internal use only. Function to
 *                                          call instead of `fetch()`.
 *                                          Must return a control descriptor.
 */
export const deleteRecord =
	( name, recordId, query, { fetchRequest = apiFetch } = {} ) =>
	async ( { select, dispatch } ) => {
		let error, response;
		const entity = await select.getEntity( name );
		if ( ! entity ) {
			return Promise.reject( `Could not find any entity named "${ name }" please check entities.` );
		}

		dispatch( {
			type: 'DELETE_RECORD_START',
			name,
			recordId,
		} );

		try {
			const path = addQueryArgs( `${ entity.baseURL }/${ recordId }`, query );
			response = await fetchRequest( {
				path,
				method: 'DELETE',
			} );
			await dispatch( removeRecords( name, recordId, true ) );
		} catch ( _error ) {
			error = _error;
		}

		dispatch( {
			type: 'DELETE_RECORD_FINISH',
			name,
			recordId,
			error,
		} );

		return response;
	};

/**
 * Action triggered to save an entity record.
 *
 * @param {string} name          Name of the received entity.
 * @param {Object} record        Record to be saved.
 * @param {Object} customRequest Internal use only. Function to call instead of `fetch()`.
 *                               Must return a control descriptor.
 */
export const saveRecord =
	( name, record, customRequest = apiFetch ) =>
	async ( { select, dispatch } ) => {
		let error, response;
		try {
			const entity = await select.getEntity( name );
			if ( ! entity ) {
				return Promise.reject( `Could not find any entity named "${ name }" please check entities.` );
			}

			const key = entity?.key ?? DEFAULT_KEY;
			const recordId = record[ key ];
			const invalidateCache = ! isEmpty( recordId );
			for ( const [ _key, value ] of Object.entries( record ) ) {
				if ( typeof value === 'function' ) {
					const evaluatedValue = value( await select.getRecord( name, recordId ) );
					await dispatch( editRecord( name, recordId, { [ _key ]: evaluatedValue } ) );
					record[ key ] = evaluatedValue;
				}
			}

			await dispatch( {
				type: 'SAVE_RECORD_START',
				name,
				record,
			} );

			const path = `${ entity.baseURL }${ recordId ? '/' + recordId : '' }`;
			const options = {
				path,
				method: recordId ? 'PUT' : 'POST',
				data: record,
			};
			response = await customRequest( options );
			await dispatch( receiveRecords( name, response, undefined, key, invalidateCache, record ) );
		} catch ( _error ) {
			error = _error;
		}

		dispatch( {
			type: 'SAVE_RECORD_FINISH',
			name,
			record,
			error,
		} );

		return response;
	};

/**
 * Returns an action object that triggers an
 * edit to an entity record.
 *
 * @param {string}          name  Name of the edited entity record.
 * @param {string | number} key   Record ID of the edited entity record.
 * @param {Object}          edits The edits.
 *
 * @return {Object} Action object.
 */
export const editRecord =
	( name, key, edits ) =>
	async ( { dispatch, resolveSelect, select } ) => {
		try {
			const record = await select.getRecord( name, key );
			const editedRecord = await select.getEditedRecord( name, key );
			const nextRecord = {
				name,
				key,
				edits: {
					...record,
					...editedRecord,
					...edits,
				},
			};

			await dispatch( {
				type: 'EDIT_RECORD',
				...nextRecord,
			} );
		} catch ( error ) {
			console.log( error );
		}
	};
