/**
 * External dependencies
 */
import {uniq, find} from 'lodash';

/**
 * WordPress dependencies
 */
import {apiFetch} from '@wordpress/data-controls';
import {addQueryArgs} from '@wordpress/url';

/**
 * Internal dependencies
 */
import {
	receiveCurrentUser,
	receiveEntityRecords,
	receiveEntityTotal,
	receiveUserQuery,
	receiveRoutes,
} from './actions';
import {fetchFromAPIWithTotal, select, resolveSelect} from './controls';
import {STORE_KEY, API_NAMESPACE} from './constants';
import {DEFAULT_ENTITY_KEY, defaultEntities } from './entities';

import {getNormalizedCommaSeparable} from './utils';

/**
 * Requests the current user from the REST API.
 */
export function* getCurrentUser() {
	const currentUser = yield apiFetch({path: '/wp/v2/users/me'});
	yield receiveCurrentUser(currentUser);
}

/**
 * Requests authors from the REST API.
 *
 * @param {Object|undefined} query Optional object of query parameters to
 *                                include with request.
 */
export function* getUsers(query = {}) {
	const path = addQueryArgs('/wp/v2/users/?per_page=100', query);
	const users = yield apiFetch({path});
	yield receiveUserQuery(path, users);
}

/**
 * Requests an entity's record from the REST API.
 *
 * @param {string}           name  Entity name.
 * @param {number|string}    key   Record's key
 * @param {Object|undefined} query Optional object of query parameters to
 *                                 include with request.
 */
export function* getEntityRecord(name, key = '', query = {}) {
	const route = yield resolveSelect(STORE_KEY, 'getRoute', name);
	if (!route) {
		return;
	}

	if (query !== undefined && query._fields) {
		// If requesting specific fields, items and query assocation to said
		// records are stored by ID reference. Thus, fields must always include
		// the ID.
		query = {
			...query,
			_fields: uniq([
				...(getNormalizedCommaSeparable(query._fields) || []),
				route.key || DEFAULT_ENTITY_KEY,
			]).join(),
		};
	}

	// Disable reason: While true that an early return could leave `path`
	// unused, it's important that path is derived using the query prior to
	// additional query modifications in the condition below, since those
	// modifications are relevant to how the data is tracked in state, and not
	// for how the request is made to the REST API.

	// eslint-disable-next-line @wordpress/no-unused-vars-before-return
	const path = addQueryArgs(route.endpoint + '/' + key, {
		...query,
		context: 'edit',
	});

	if (query !== undefined) {
		query = {...query, include: [key]};

		// The resolution cache won't consider query as reusable based on the
		// fields, so it's tested here, prior to initiating the REST request,
		// and without causing `getEntityRecords` resolution to occur.
		const hasRecords = yield select(
			STORE_KEY,
			'hasEntityRecords',
			name,
			query
		);
		if (hasRecords) {
			return;
		}
	}

	const record = yield apiFetch({path});
	console.log(record);
	console.log({name, record, query});
	yield receiveEntityRecords(name, record, query);
}

/**
 * Requests the entity's records from the REST API.
 *
 * @param {string}  name   Entity name.
 * @param {Object?} query  Query Object.
 */
export function* getEntityRecords(name, query = {}) {
	const route = yield resolveSelect(STORE_KEY, 'getRoute', name);
	if (!route) {
		return;
	}
	if (query._fields) {
		// If requesting specific fields, items and query assocation to said
		// records are stored by ID reference. Thus, fields must always include
		// the ID.
		query = {
			...query,
			_fields: uniq([
				...(getNormalizedCommaSeparable(query._fields) || []),
				route.key || DEFAULT_ENTITY_KEY,
			]).join(),
		};
	}

	const path = addQueryArgs(route.endpoint, {
		...query,
		context: 'edit',
	});

	const {items, total} = yield fetchFromAPIWithTotal(path);
	let records = items;
	// If we request fields but the result doesn't contain the fields,
	// explicitly set these fields as "undefined"
	// that way we consider the query "fulfilled".
	if (query._fields) {
		records = items.map((record) => {
			query._fields.split(',').forEach((field) => {
				if (!record.hasOwnProperty(field)) {
					record[field] = undefined;
				}
			});

			return record;
		});
	}

	yield receiveEntityRecords(name, records, query);
	yield receiveEntityTotal(name, total, query);
	// When requesting all fields, the list of results can be used to
	// resolve the `getEntityRecord` selector in addition to `getEntityRecords`.
	// See https://github.com/WordPress/gutenberg/pull/26575
	if (!query?._fields) {
		const key = route.key || DEFAULT_ENTITY_KEY;
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
}

getEntityRecords.shouldInvalidate = (action, name) => {
	return (
		(action.type === 'RECEIVE_ITEMS' || action.type === 'REMOVE_ITEMS') &&
		action.invalidateCache &&
		name === action.name
	);
};

/**
 * Get entity total.
 *
 * @param {string} name
 * @param {Object} query
 * @param defaults
 * @return {number}
 */
export function* getEntityTotal(name, query = {}, defaults = 0) {
	yield resolveSelect(STORE_KEY, 'getEntityRecords', name, query);
	return yield select(
		STORE_KEY,
		'getEntityTotal',
		name,
		query,
		defaults
	);
}
/**
 * Resolver for getRoute
 *
 * @return {Generator<*, void, ?>}
 */
export function* getRoute(name) {
	yield resolveSelect(STORE_KEY, 'getRoutes');
	return yield select(STORE_KEY, 'getRoute', name);
}

/**
 * Resolver for the getRoutes
 *
 * @return {Generator<Object|{routes: Object, type: string}|{}|{routes: Object, type: string}|{routes: Object, type: string}|*, void, ?>}
 */
export function* getRoutes() {
	const response = yield apiFetch({path: API_NAMESPACE});
	const schemaRoutes = response && response.routes ? Object.keys(response.routes) : [];
	const routes = yield Object.values(schemaRoutes.reduce((memo, route) => {
		const endpoint = route.replace(/\/\(\?P\<[a-z_]*\>\[\\*[a-z]\-?\]\+\)/g, '');
		const name = endpoint.replace(`${API_NAMESPACE}/`, '');
		const setup = find(defaultEntities, {name}) || {key: 'id'}
		if (name && name !== API_NAMESPACE && !memo[name]) {
			memo[name] = {name, endpoint: endpoint, ...setup}
		}
		return memo;
	}, {}));

	yield receiveRoutes(routes);
	return routes;
}

