/**
 * External dependencies
 */
import { castArray, get, isEqual, find } from 'lodash';
import { v4 as uuid } from 'uuid';

/**
 * WordPress dependencies
 */
import { controls } from '@wordpress/data';
import { apiFetch, __unstableAwaitPromise } from '@wordpress/data-controls';
import { addQueryArgs } from '@wordpress/url';


/**
 * Internal dependencies
 */
import { receiveItems, removeItems, receiveQueriedItems } from './queried-data';
import { getKindEntities } from './entities';
import { DEFAULT_ENTITY_KEY } from './contants';
import {
	__unstableAcquireStoreLock,
	__unstableReleaseStoreLock,
} from './locks';
import { createBatch } from './batch';
import { getDispatch } from './controls';

/**
 * Returns an action object used in adding new entities.
 *
 * @param {Array} entities  Entities received.
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
 * @param {?boolean}     invalidateCache Should invalidate query caches.
 * @param {?Object}      edits           Edits to reset.
 * @return {Object} Action object.
 */
export function receiveEntityRecords(
	name,
	records,
	query,
	invalidateCache = false,
	edits
) {
	let action;
	if ( query ) {
		action = receiveQueriedItems( records, query, edits );
	} else {
		action = receiveItems( records, edits );
	}

	return {
		...action,
		name,
		invalidateCache,
	};
}
