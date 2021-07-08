/**
 * External dependencies
 */
import { uniq, pickBy, identity } from 'lodash';

/**
 * WordPress dependencies
 */

import { addQueryArgs } from '@wordpress/url';
/**
 * Internal dependencies
 */
import { STORE_NAME } from '../constants';
import { DEFAULT_ENTITY_KEY } from '../entities';
import { getNormalizedCommaSeparable } from '../utils';
import {
	receiveEntityRecords,
	receiveTotalEntityRecords,
	receiveEntityError,
} from './actions';
import {
	apiFetch,
	apiFetchWithHeaders,
	select,
	resolveSelect,
} from '../controls';

/**
 * Requests an entity's record from the REST API.
 *
 * @param {string}           name  Entity name.
 * @param {number|string}    key   Record's key
 * @param {Object|undefined} query Optional object of query parameters to
 *                                 include with request.
 */
export function* getEntityRecord( name, key = '', query = {} ) {
	const entity = yield select( STORE_NAME, 'getEntity', name );
	if ( ! entity ) {
		throw `Could not find any entity named "${ name }" please check entity config`;
	}

	if ( query && query.hasOwnProperty( '_fields' ) ) {
		// If requesting specific fields, items and query association to said
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

	if ( query && query.hasOwnProperty( '_fields' ) ) {
		query = { ...query, include: [ key ] };

		// The resolution cache won't consider query as reusable based on the
		// fields, so it's tested here, prior to initiating the REST request,
		// and without causing `getEntities` resolution to occur.
		const hasRecords = yield select(
			STORE_NAME,
			'hasEntityRecords',
			name,
			query
		);
		if ( hasRecords ) {
			return;
		}
	}
	const path = addQueryArgs( entity.endpoint + '/' + key, {
		...pickBy( query, identity ),
	} );
	try {
		const record = yield apiFetch( { path } );
		yield receiveEntityRecords( name, record, query );
		yield receiveEntityError(
			name,
			{},
			{
				...query,
				key,
			}
		);
		return record;
	} catch ( error ) {
		yield receiveEntityError( name, error, {
			...query,
			key,
		} );
		return {};
	}
}

/**
 * Requests the entity's records from the REST API.
 *
 * @param {string}  name   Entity name.
 * @param {Object?} query  Query Object.
 */
export function* getEntityRecords( name, query = {} ) {
	const entity = yield select( STORE_NAME, 'getEntity', name );
	if ( ! entity ) {
		throw `Could not find any entity named "${ name }" please check entity config`;
	}
	if ( query && query.hasOwnProperty( '_fields' ) ) {
		// If requesting specific fields, items and query association to said
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
		...pickBy( query, identity ),
		context: 'edit',
	} );

	try {
		const { data, headers } = yield apiFetchWithHeaders( { path } );
		const total = parseInt( headers.get( 'x-wp-total' ), 10 );
		let records = data;
		// If we request fields but the result doesn't contain the fields,
		// explicitly set these fields as "undefined"
		// that way we consider the query "fulfilled".
		if ( query && query.hasOwnProperty( '_fields' ) ) {
			records = records.map( ( record ) => {
				query._fields.split( ',' ).forEach( ( field ) => {
					if ( ! record.hasOwnProperty( field ) ) {
						record[ field ] = undefined;
					}
				} );

				return record;
			} );
		}
		yield receiveEntityRecords( name, records, query );
		yield receiveTotalEntityRecords( name, total, query );
		yield receiveEntityError( name, {}, query );
		// When requesting all fields, the list of results can be used to
		// resolve the `getEntity` selector in addition to `getEntities`.
		// See https://github.com/WordPress/gutenberg/pull/26575
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

		return records;
	} catch ( error ) {
		yield receiveEntityError( name, error, query );
		return [];
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
 * Get entity count.
 *
 * @param {string} name
 * @param {Object} query
 * @param {number} defaults
 * @return {number} Get total number.
 */
export function* getTotalEntityRecords(
	name,
	query = {},
	defaults = undefined
) {
	yield resolveSelect( STORE_NAME, 'getEntityRecords', name, query );
	return yield select(
		STORE_NAME,
		'getTotalEntityRecords',
		name,
		query,
		defaults
	);
}
