/**
 * External dependencies
 */
import { castArray } from 'lodash';

/**
 * Returns an action object used in signalling that items have been received.
 *
 * @param {Array}   items Items received.
 * @param {?string}		 primaryKey   Primary key of the item.
 * @param {?Object} edits Optional edits to reset.
 *
 * @return {Object} Action object.
 */
export function receiveItems( items, primaryKey, edits = {} ) {
	return {
		type: 'RECEIVE_ITEMS',
		items: castArray( items ),
		primaryKey,
		persistedEdits: edits,
	};
}

/**
 * Returns an action object used in signalling that queried data has been
 * received.
 *
 * @param {Array}   items Queried items received.
 * @param {?Object} query Optional query object.
 * @param {?string}		 primaryKey   Primary key of the item.
 * @param {?Object} edits Optional edits to reset.
 * @return {Object} Action object.
 */
export function receiveQueriedItems( items, query = {}, primaryKey, edits ) {
	return {
		...receiveItems( items, primaryKey, edits ),
		query,
	};
}

/**
 * Returns an action object used in signalling that entity records have been
 * deleted and they need to be removed from entities state.
 *
 * @param {string}       name             Name of the removed entities.
 * @param {Array|number} records          Record IDs of the removed entities.
 * @param {boolean}      invalidateCache  Controls whether we want to invalidate the cache.
 * @return {Object} Action object.
 */
export function removeItems( name, records, invalidateCache = false ) {
	return {
		type: 'REMOVE_ITEMS',
		itemIds: castArray( records ),
		name,
		invalidateCache,
	};
}

/**
 * Returns an action object used in signalling that items total have been received.
 *
 * @param {number}   total Queried item total received.
 * @param {?Object} query Optional query object.
 *
 * @return {Object} Action object.
 */
export function receiveItemTotal( total, query = {} ) {
	return {
		type: 'RECEIVE_TOTAL',
		total: parseInt( total, 10 ),
		query,
	};
}

/**
 * Returns an action object used in signalling that errors have been received.
 *
 * @param {Object}   error Queried item total received.
 * @param {?Object} query Optional query object.
 *
 * @return {Object} Action object.
 */
export function receiveError( error, query = {} ) {
	return {
		type: 'RECEIVE_ERROR',
		error,
		query,
	};
}
