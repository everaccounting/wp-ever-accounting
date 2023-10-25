/**
 * WordPress dependencies
 */
import apiFetch from '@wordpress/api-fetch';
import { addQueryArgs } from '@wordpress/url';

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
 * Returns an action object used in signalling that errors have been received.
 *
 * @param {string}  name   Name of the entity.
 * @param {string}  action Action name when the error occurred.
 * @param {Object}  error  Queried item total received.
 * @param {?Object} query  Optional query object.
 *
 * @return {Object} Action object.
 */
export function receiveError( name, action, error, query = {} ) {
	return {
		type: 'RECEIVE_ERROR',
		name,
		action,
		error,
		query,
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
 * @param {Function} [options.fetchRequest] Internal use only. Function to
 *                                           call instead of `fetch()`.
 *                                           Must return a control descriptor.
 */
export const deleteRecord =
	( name, recordId, query, { fetchRequest = apiFetch } = {} ) =>
	async ( { select, dispatch } ) => {
		let error, response;
		const entity = await select.getConfig( name );
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
