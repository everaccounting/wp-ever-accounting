import {find, uniq} from "lodash";
import {entities, DEFAULT_ENTITY_KEY} from "./entities";
import {getNormalizedCommaSeparable} from "../utils";
import { addQueryArgs } from '@wordpress/url';
import {receiveEntityRecords, receiveTotalEntityRecords} from "./actions";
import {fetchWithHeaders, select, resolveSelect} from "../controls";
import {STORE_NAME} from "./constants";

/**
 * Requests an entity's record from the REST API.
 *
 * @param {string}           name  Entity name.
 * @param {number|string}    key   Record's key
 * @param {Object|undefined} query Optional object of query parameters to
 *                                 include with request.
 */
export function* getEntityRecord(name, key = '', query = {}) {
	const entity = find( entities, { name } );
	if ( ! entity ) {
		throw (`Could not find any entity named "${name}" please check entity config`);
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

	const path = addQueryArgs( entity.endpoint + '/' + key, {
		...entity.queryDefaults,
		...query,
	} );

	if (query !== undefined) {
		query = { ...query, include: [key] };

		// The resolution cache won't consider query as reusable based on the
		// fields, so it's tested here, prior to initiating the REST request,
		// and without causing `getEntities` resolution to occur.
		const hasRecords = yield select(STORE_NAME, 'hasEntityRecords', name, query);
		if (hasRecords) {
			return;
		}
	}

	const {data:record} = yield fetchWithHeaders( { path } );
	yield receiveEntityRecords( name, record, query );
	return record;
}

/**
 * Requests the entity's records from the REST API.
 *
 * @param {string}  name   Entity name.
 * @param {Object?} query  Query Object.
 */
export function* getEntityRecords(name, query = {}) {
	const entity = find( entities, { name } );
	if ( ! entity ) {
		throw (`Could not find any entity named "${name}" please check entity config`);
	}
	if ( query._fields ) {
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
		...query,
		context: 'edit',
	} );

	const {headers, data} = yield fetchWithHeaders( { path } );
	const total = parseInt( headers.get( 'x-wp-total' ), 10 );
	let records = data;
	// If we request fields but the result doesn't contain the fields,
	// explicitly set these fields as "undefined"
	// that way we consider the query "fulfilled".
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
	yield receiveEntityRecords(name, records, query);
	yield receiveTotalEntityRecords(name, total, query);
	// When requesting all fields, the list of results can be used to
	// resolve the `getEntity` selector in addition to `getEntities`.
	// See https://github.com/WordPress/gutenberg/pull/26575
	if (!query?._fields) {
		const key = entity.key || DEFAULT_ENTITY_KEY;
		for (const record of records) {
			if (record[key]) {
				yield {
					type: 'START_RESOLUTION',
					selectorName: 'getEntityRecord',
					args: [name, record[key]],
				};
				yield {
					type: 'FINISH_RESOLUTION',
					selectorName: 'getEntityRecord',
					args: [name, record[key]],
				};
			}
		}
	}

	return records;
}


getEntityRecords.shouldInvalidate = (action, name) => {
	return (
		(action.type === 'RECEIVE_ITEMS' || action.type === 'REMOVE_ITEMS') &&
		action.invalidateCache &&
		name === action.name
	);
};

/**
 * Get entity count.
 *
 * @param {string} name
 * @param {Object} query
 * @param defaults
 * @return {number}
 */
export function* getTotalEntityRecords(name, query = {}, defaults = undefined) {
	yield resolveSelect(STORE_NAME, 'getEntityRecords', name, query);
	return yield select(STORE_NAME, 'getTotalEntityRecords', name, query, defaults);
}
