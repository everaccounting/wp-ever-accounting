/**
 * External dependencies
 */
import { uniq } from 'lodash';

import { apiFetch } from '@wordpress/data-controls';
import { addQueryArgs } from '@wordpress/url';

import {
	receiveCurrentUser,
	receiveEntityRecords,
	receiveEntityTotal,
	receiveUserQuery,
} from './actions';
import { fetchFromAPIWithTotal, select, resolveSelect } from './controls';
import { STORE_KEY } from './constants';
import {DEFAULT_ENTITY_KEY, getMethodName} from './entities';

import { getNormalizedCommaSeparable } from './utils';

/**
 * Requests the current user from the REST API.
 */
export function* getCurrentUser() {
	const currentUser = yield apiFetch( { path: '/wp/v2/users/me' } );
	yield receiveCurrentUser( currentUser );
}

/**
 * Requests authors from the REST API.
 *
 * @param {Object|undefined} query Optional object of query parameters to
 *                                include with request.
 */
export function* getUsers( query={} ) {
	const path = addQueryArgs( '/wp/v2/users/?per_page=100', query );
	const users = yield apiFetch( { path } );
	yield receiveUserQuery( path, users );
}

/**
 * Requests an entity's record from the REST API.
 *
 * @param {string}           name  Entity name.
 * @param {number|string}    key   Record's key
 * @param {Object|undefined} query Optional object of query parameters to
 *                                 include with request.
 */
export function* getEntityRecord( name, key = '', query={} ) {
	const entity = yield select( STORE_KEY, 'getEntity', name );
	if ( ! entity ) {
		return;
	}

	if ( query !== undefined && query._fields ) {
		// If requesting specific fields, items and query assocation to said
		// records are stored by ID reference. Thus, fields must always include
		// the ID.
		query = {
			...query,
			_fields: uniq( [
				...( getNormalizedCommaSeparable( query._fields ) || [] ),
				entity.key || DEFAULT_ENTITY_KEY,
			] ).join(),
		};
	}

	// Disable reason: While true that an early return could leave `path`
	// unused, it's important that path is derived using the query prior to
	// additional query modifications in the condition below, since those
	// modifications are relevant to how the data is tracked in state, and not
	// for how the request is made to the REST API.

	// eslint-disable-next-line @wordpress/no-unused-vars-before-return
	const path = addQueryArgs( entity.endpoint + '/' + key, {
		...query,
		context: 'edit',
	} );

	if ( query !== undefined ) {
		query = { ...query, include: [ key ] };

		// The resolution cache won't consider query as reusable based on the
		// fields, so it's tested here, prior to initiating the REST request,
		// and without causing `getEntityRecords` resolution to occur.
		const hasRecords = yield select(
			STORE_KEY,
			'hasEntityRecords',
			name,
			query
		);
		if ( hasRecords ) {
			return;
		}
	}

	const record = yield apiFetch( { path } );
	yield receiveEntityRecords( name, record, query );
}

/**
 * Requests the entity's records from the REST API.
 *
 * @param {string}  name   Entity name.
 * @param {Object?} query  Query Object.
 */
export function* getEntityRecords( name, query = {} ) {
	const entity = yield select( STORE_KEY, 'getEntity', name );
	if ( ! entity ) {
		return;
	}
	if ( query._fields ) {
		// If requesting specific fields, items and query assocation to said
		// records are stored by ID reference. Thus, fields must always include
		// the ID.
		query = {
			...query,
			_fields: uniq( [
				...( getNormalizedCommaSeparable( query._fields ) || [] ),
				entity.key || DEFAULT_ENTITY_KEY,
			] ).join(),
		};
	}

	const path = addQueryArgs( entity.endpoint, {
		...query,
		context: 'edit',
	} );

	const { items, total } = yield fetchFromAPIWithTotal( path );
	let records = items;
	// If we request fields but the result doesn't contain the fields,
	// explicitly set these fields as "undefined"
	// that way we consider the query "fulfilled".
	if ( query._fields ) {
		records = items.map( ( record ) => {
			query._fields.split( ',' ).forEach( ( field ) => {
				if ( ! record.hasOwnProperty( field ) ) {
					record[ field ] = undefined;
				}
			} );

			return record;
		} );
	}

	yield receiveEntityRecords( name, records, query );
	yield receiveEntityTotal( name, total, query );
	// // When requesting all fields, the list of results can be used to
	// // resolve the `getEntityRecord` selector in addition to `getEntityRecords`.
	// // See https://github.com/WordPress/gutenberg/pull/26575
	if ( ! query?._fields ) {
		const key = entity.key || DEFAULT_ENTITY_KEY;
		for ( const record of records ) {
			if ( record[ key ] ) {
				yield {
					type: 'START_RESOLUTION',
					selectorName: 'getEntityRecord',
					args: [ name, record[ key ] ],
				};
				yield {
					type: 'FINISH_RESOLUTION',
					selectorName: 'getEntityRecord',
					args: [ name, record[ key ] ],
				};
			}
		}
	}
}

getEntityRecords.shouldInvalidate = ( action, name ) => {
	return (
		( action.type === 'RECEIVE_ITEMS' || action.type === 'REMOVE_ITEMS' ) &&
		action.invalidateCache &&
		name === action.name
	);
};

/**
 * Get entity total.
 *
 * @param {string} name
 * @param {object} query
 * @returns {number}
 */
export function* getEntityTotal( name, query = {} ) {
	yield resolveSelect( STORE_KEY, getMethodName( name, 'get', true ), query );
	return yield select( STORE_KEY, getMethodName( name, 'getTotal', true ), query );
}
