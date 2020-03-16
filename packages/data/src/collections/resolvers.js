/**
 * External dependencies
 */
import {select} from '@wordpress/data-controls';
import {addQueryArgs} from '@wordpress/url';

/**
 * Internal dependencies
 */
import {receiveCollection, receiveCollectionError} from './actions';
import {STORE_KEY as SCHEMA_STORE_KEY} from '../schema/constants';
import {STORE_KEY, DEFAULT_EMPTY_ARRAY} from './constants';
import {apiFetchWithHeaders} from './controls';
import {invalidateCollection} from './actions';

/**
 * Resolver for retrieving a collection via a api route.
 *
 * @param {string} namespace
 * @param {string} resourceName
 * @param {Object} query
 * @param {Array}  ids
 */
export function* getCollection(resourceName, query, ids= []) {
	const route = yield select(
		SCHEMA_STORE_KEY,
		'getRoute',
		resourceName,
		ids
	);
	const queryString = addQueryArgs('', query);
	if (!route) {
		yield receiveCollection(resourceName, queryString, ids);
		return;
	}

	try {
		const {
			items = DEFAULT_EMPTY_ARRAY,
			headers,
		} = yield apiFetchWithHeaders(route + queryString);

		if (headers && headers.get && headers.has('last-modified')) {
			// Do any invalidation before the collection is received to prevent
			// this query running again.
			yield invalidateCollection(
				parseInt(headers.get('last-modified'), 10)
			);
		}

		yield receiveCollection(resourceName, queryString, ids, {
			items,
			headers,
		});
	} catch (error) {
		yield receiveCollectionError(
			resourceName,
			queryString,
			ids,
			error
		);
	}
}

/**
 * Resolver for retrieving a specific collection header for the given arguments
 *
 * Note: This triggers the `getCollection` resolver if it hasn't been resolved
 * yet.
 *
 * @param {string} header
 * @param {string} namespace
 * @param {string} resourceName
 * @param {Object} query
 * @param {Array}  ids
 */
export function* getCollectionHeader(
	header,
	resourceName,
	query,
	ids=[]
) {
	// feed the correct number of args in for the select so we don't resolve
	// unnecessarily. Any undefined args will be excluded. This is important
	// because resolver resolution is cached by both number and value of args.
	const args = [resourceName, query].filter(
		(arg) => typeof arg !== 'undefined'
	);
	console.log(args);
	//we call this simply to do any resolution of the collection if necessary.
	yield select(STORE_KEY, 'getCollection', ...args);
}
