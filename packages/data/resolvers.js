/**
 * External dependencies
 */
import { find, includes, get, hasIn, compact, uniq } from 'lodash';

/**
 * WordPress dependencies
 */
import { addQueryArgs } from '@wordpress/url';
import { controls } from '@wordpress/data';
import { apiFetch } from '@wordpress/data-controls';
import {STORE_NAME, DEFAULT_ENTITY_KEY} from "./contants";
import {resolveSelect, select} from "./controls";

/**
 * Internal dependencies
 */
import {
	// receiveUserQuery,
	// receiveCurrentTheme,
	// receiveCurrentUser,
	receiveEntityRecords,
	// receiveThemeSupports,
	// receiveEmbedPreview,
	// receiveUserPermission,
	// receiveAutosaves,
} from './actions';
import { ifNotResolved, getNormalizedCommaSeparable } from './utils';
import {
	__unstableAcquireStoreLock,
	__unstableReleaseStoreLock,
} from './locks';


/**
 * Requests the entity's records from the REST API.
 *
 * @param {string}  name   Entity name.
 * @param {Object?} query  Query Object.
 */
export function* getEntityRecords(  name, query = {} ) {
	console.log('getEntityRecords')
	const entities = yield select( STORE_NAME, 'getEntitiesByName', name );
	console.log(entities);
	const entity = find( entities, { name } );
	if ( ! entity ) {
		return;
	}

	const lock = yield __unstableAcquireStoreLock(
		STORE_NAME,
		[ 'entities', 'data', name ],
		{ exclusive: false }
	);
	console.log(lock);
	try {
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

		const path = addQueryArgs( entity.baseURL, {
			...query,
			context: 'edit',
		} );

		let records = Object.values( yield apiFetch( { path } ) );

		// If we request fields but the result doesn't contain the fields,
		// explicitely set these fields as "undefined"
		// that way we consider the query "fullfilled".
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


		yield receiveEntityRecords( name, records, query );
		// When requesting all fields, the list of results can be used to
		// resolve the `getEntityRecord` selector in addition to `getEntityRecords`.
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
	} finally {
		yield* __unstableReleaseStoreLock( lock );
	}
}

getEntityRecords.shouldInvalidate = ( action, kind, name ) => {
	return (
		( action.type === 'RECEIVE_ITEMS' || action.type === 'REMOVE_ITEMS' ) &&
		action.invalidateCache &&
		name === action.name
	);
};
