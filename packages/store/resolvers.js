/**
 * External dependencies
 */
import {find, includes, get, hasIn, compact, uniq} from 'lodash';

/**
 * WordPress dependencies
 */
import {addQueryArgs} from '@wordpress/url';
import deprecated from '@wordpress/deprecated';
import {controls} from '@wordpress/data';
// import {apiFetch} from '@wordpress/data-controls';
import apiFetch from '@wordpress/api-fetch';
/**
 * Internal dependencies
 */
import {regularFetch, fetchFromAPIWithTotal} from './controls';

/**
 * Internal dependencies
 */
import {
	receiveEntityRecords,
} from './actions';
import {getKindEntities, DEFAULT_ENTITY_KEY} from './entities';
import {ifNotResolved, getNormalizedCommaSeparable} from './utils';
import {
	__unstableAcquireStoreLock,
	__unstableReleaseStoreLock,
} from './locks';

/**
 * Requests an entity's record from the REST API.
 *
 * @param {string}           kind  Entity kind.
 * @param {string}           name  Entity name.
 * @param {number|string}    key   Record's key
 * @param {Object|undefined} query Optional object of query parameters to
 *                                 include with request.
 */
export function* getEntityRecord(kind, name, key = '', query) {
	const entities = yield getKindEntities(kind);
	const entity = find(entities, {kind, name});
	if (!entity) {
		return;
	}

	const lock = yield * __unstableAcquireStoreLock(
		'ea/store',
		['entities', 'data', kind, name, key],
		{exclusive: false}
	);
	try {
		if (query !== undefined && query._fields) {
			// If requesting specific fields, items and query assocation to said
			// records are stored by ID reference. Thus, fields must always include
			// the ID.
			query = {
				...query,
				_fields: uniq([
					...(getNormalizedCommaSeparable(query._fields) || []),
					entity.key || DEFAULT_ENTITY_KEY,
				]).join(),
			};
		}

		// Disable reason: While true that an early return could leave `path`
		// unused, it's important that path is derived using the query prior to
		// additional query modifications in the condition below, since those
		// modifications are relevant to how the data is tracked in state, and not
		// for how the request is made to the REST API.

		// eslint-disable-next-line @wordpress/no-unused-vars-before-return
		const path = addQueryArgs(entity.baseURL + '/' + key, {
			...query,
			context: 'edit',
		});

		if (query !== undefined) {
			query = {...query, include: [key]};

			// The resolution cache won't consider query as reusable based on the
			// fields, so it's tested here, prior to initiating the REST request,
			// and without causing `getEntityRecords` resolution to occur.
			const hasRecords = yield controls.select(
				'ea/store',
				'hasEntityRecords',
				kind,
				name,
				query
			);
			if (hasRecords) {
				return;
			}
		}

		const record = yield apiFetch({path});
		yield receiveEntityRecords(kind, name, record, query);
	} catch (error) {
		// We need a way to handle and access REST API errors in state
		// Until then, catching the error ensures the resolver is marked as resolved.
	} finally {
		yield* __unstableReleaseStoreLock(lock);
	}
}

/**
 * Requests an entity's record from the REST API.
 */
export const getRawEntityRecord = ifNotResolved(
	getEntityRecord,
	'getEntityRecord'
);

/**
 * Requests an entity's record from the REST API.
 */
export const getEditedEntityRecord = ifNotResolved(
	getRawEntityRecord,
	'getRawEntityRecord'
);

/**
 * Requests the entity's records from the REST API.
 *
 * @param {string}  kind   Entity kind.
 * @param {string}  name   Entity name.
 * @param {Object?} query  Query Object.
 */
export function* getEntityRecords(kind, name, query = {}) {
	const entities = yield getKindEntities(kind);
	const entity = find(entities, {kind, name});
	if (!entity) {
		return;
	}

	const lock = yield * __unstableAcquireStoreLock(
		'ea/store',
		['entities', 'data', kind, name],
		{exclusive: false}
	);
	try {
		if (query._fields) {
			// If requesting specific fields, items and query assocation to said
			// records are stored by ID reference. Thus, fields must always include
			// the ID.
			query = {
				...query,
				_fields: uniq([
					...(getNormalizedCommaSeparable(query._fields) || []),
					entity.key || DEFAULT_ENTITY_KEY,
				]).join(),
			};
		}

		const path = addQueryArgs(entity.baseURL, {
			...query,
			context: 'edit',
		});


		let {items, total} = yield fetchFromAPIWithTotal(path);
		let records = items;
		// If we request fields but the result doesn't contain the fields,
		// explicitly set these fields as "undefined"
		// that way we consider the query "fulfilled".
		console.log(items);
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

		yield receiveEntityRecords( kind, name, records, query );
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
						args: [ kind, name, record[ key ] ],
					};
					yield {
						type: 'FINISH_RESOLUTION',
						selectorName: 'getEntityRecord',
						args: [ kind, name, record[ key ] ],
					};
				}
			}
		}
	} finally {
		yield* __unstableReleaseStoreLock(lock);
	}
}

getEntityRecords.shouldInvalidate = (action, kind, name) => {
	return (
		(action.type === 'RECEIVE_ITEMS' || action.type === 'REMOVE_ITEMS') &&
		action.invalidateCache &&
		kind === action.kind &&
		name === action.name
	);
};

/**
 * Requests the current theme.
 */
export function* getCurrentTheme() {
	const activeThemes = yield apiFetch({
		path: '/wp/v2/themes?status=active',
	});
	yield receiveCurrentTheme(activeThemes[0]);
}
