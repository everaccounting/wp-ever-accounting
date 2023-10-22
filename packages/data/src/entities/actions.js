/**
 * WordPress dependencies
 */
import apiFetch from '@wordpress/api-fetch';
import { addQueryArgs } from '@wordpress/url';
/**
 * External dependencies
 */
import { isEmpty } from 'lodash';
import fastDeepEqual from 'fast-deep-equal/es6';

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
		entities,
	};
}

/**
 * Returns an action object used in signalling that entity records have been received.
 *
 * @param {string}       name            Name of the received entity.
 * @param {Array|Object} records         Records received.
 * @param {?Object}      query           Query Object.
 * @param {?string}      primaryKey      Primary key of the item.
 * @param {?boolean}     invalidateCache Should invalidate query caches.
 * @param {?Object}      edits           Edits to reset.
 * @return {Object} Action object.
 */
export function receiveRecords( name, records, query = {}, primaryKey = 'id', invalidateCache = false, edits ) {
	return {
		type: 'RECEIVE_RECORDS',
		records: Array.isArray( records ) ? records : [ records ],
		name,
		query,
		primaryKey,
		invalidateCache,
		edits,
	};
}

/**
 * Returns an action object used in signalling that entity records count have been received.
 *
 * @param { string } name            Entity Name
 * @param { number } count           count
 * @param {Object}   query
 * @param {boolean}  invalidateCache
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
 * Returns an action object used in signalling that errors have been received.
 *
 * @param { string } name  Entity Name
 * @param {Object}   error Queried item total received.
 * @param {?Object}  query Optional query object.
 *
 * @return {Object} Action object.
 */
export function receiveRecordsError( name, error, query = {} ) {
	return {
		type: 'RECEIVE_RECORDS_ERROR',
		name,
		error,
		query,
	};
}

/**
 * Returns an action object used in signalling that entity records have been
 * deleted and they need to be removed from entities state.
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
 * Action triggered to delete an entity record.
 *
 * @param {string}   name                    Name of the deleted entity.
 * @param {string}   recordId                Record ID of the deleted entity.
 * @param {?Object}  query                   Special query parameters for the
 *                                           DELETE API call.
 * @param {Object}   [options]               Delete options.
 * @param {Function} [options.customRequest] Internal use only. Function to
 *                                           call instead of `fetch()`.
 *                                           Must return a control descriptor.
 */
export const deleteRecord =
	( name, recordId, query, { customRequest = apiFetch } = {} ) =>
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
			response = await customRequest( {
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
 * @param {string}   name                    Name of the received entity.
 * @param {Object}   record                  Record to be saved.
 * @param {Object}   [options]               Delete options.
 * @param {Function} [options.customRequest] Internal use only. Function to
 *                                           call instead of `fetch()`.
 *                                           Must return a control descriptor.
 */
export const saveRecord =
	( name, record, { customRequest = apiFetch } = {} ) =>
	async ( { select, dispatch } ) => {
		let error, response;
		const entity = await select.getEntity( name );
		if ( ! entity ) {
			return Promise.reject( `Could not find any entity named "${ name }" please check entities.` );
		}

		const { baseURL, primaryKey } = entity;
		const recordId = record[ primaryKey ];
		const invalidateCache = ! isEmpty( recordId );
		for ( const [ key, value ] of Object.entries( record ) ) {
			if ( typeof value === 'function' ) {
				const evaluatedValue = value( await select.getEditedRecord( name, recordId ) );
				await dispatch( editEntityRecord( name, recordId, { [ key ]: evaluatedValue } ) );
				record[ key ] = evaluatedValue;
			}
		}

		dispatch( {
			type: 'SAVE_RECORD_START',
			name,
			recordId,
		} );

		try {
			const path = isEmpty( recordId ) ? baseURL : `${ baseURL }/${ recordId }`;
			response = await customRequest( {
				path,
				method: isEmpty( recordId ) ? 'POST' : 'PUT',
				data: record,
			} );
			await dispatch( removeRecords( name, response, {}, primaryKey, invalidateCache ) );
		} catch ( _error ) {
			error = _error;
		}

		dispatch( {
			type: 'SAVE_RECORD_FINISH',
			name,
			recordId,
			error,
		} );

		return response;
	};

/**
 * Returns an action object that triggers an
 * edit to an entity record.
 *
 * @param {string}          name     Name of the edited entity record.
 * @param {string | number} recordId Record ID of the edited entity record.
 * @param {Object}          edits    The edits.
 *
 * @return {Object} Action object.
 */
export const editEntityRecord =
	( name, recordId, edits ) =>
	async ( { select } ) => {
		const record = await select.getRecord( name, recordId );
		const editedRecord = await select.getEditedRecord( name, recordId );
		const edit = {
			name,
			recordId,
			// Clear edits when they are equal to their persisted counterparts
			// so that the property is not considered dirty.
			edits: Object.keys( edits ).reduce( ( acc, key ) => {
				const recordValue = record[ key ];
				const editedRecordValue = editedRecord[ key ];
				const value = mergedEdits[ key ] ? { ...editedRecordValue, ...edits[ key ] } : edits[ key ];
				acc[ key ] = fastDeepEqual( recordValue, value ) ? undefined : value;
				return acc;
			}, {} ),
		};
		return {
			type: 'EDIT_RECORD',
			...edit,
		};
	};
