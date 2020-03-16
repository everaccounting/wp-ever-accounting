/**
 * External dependencies
 */
import {apiFetch, select, dispatch} from '@wordpress/data-controls';
import {isArray, isEmpty} from "lodash";
import {__} from "@wordpress/i18n";
import { NotificationManager} from 'react-notifications';

/**
 * Internal dependencies
 */
import {ACTION_TYPES as types} from './action-types';
import {STORE_KEY as SCHEMA_STORE_KEY} from '../schema/constants';
import {STORE_KEY} from "./constants";

let Headers = window.Headers || null;
Headers = Headers
	? new Headers()
	: {get: () => undefined, has: () => undefined};

/**
 * Returns an action object used in updating the store with the provided items
 * retrieved from a request using the given querystring.
 *
 * This is a generic response action.
 *
 * @param {string}   resourceName     The resource name for the collection route.
 * @param {string}   [queryString=''] The query string for the collection
 * @param {Array}    [ids=[]]         An array of ids (in correct order) for the
 *                                    model.
 * @param {Object}   [response={}]    An object containing the response from the
 *                                    collection request.
 * @param {Array<*>} response.items	An array of items for the given collection.
 * @param {Headers}  response.headers A Headers object from the response
 *                                    link https://developer.mozilla.org/en-US/docs/Web/API/Headers
 * @param {boolean}     [replace=false]  If true, signals to replace the current
 *                                    items in the state with the provided
 *                                    items.
 * @return {
 * 	{
 * 		type: string,
 * 		namespace: string,
 * 		resourceName: string,
 * 		queryString: string,
 * 		ids: Array<*>,
 * 		items: Array<*>,
 *	}
 * } Object for action.
 */
export function receiveCollection(resourceName, queryString = '', ids = [], response = {
	items: [],
	headers: Headers
}, replace = false) {
	return {
		type: replace ? types.RESET_COLLECTION : types.RECEIVE_COLLECTION,
		resourceName,
		queryString,
		ids,
		response,
	};
}

export function* create(resourceName, data = {}) {
	const route = yield select(
		SCHEMA_STORE_KEY,
		'getRoute',
		resourceName
	);

	if (!route) {
		return;
	}

	try {
		const item = yield apiFetch({
			path: route,
			method: 'POST',
			data,
			cache: 'no-store',
		});

		yield invalidateCollection(new Date().getTime());

	} catch (error) {
		NotificationManager.error(error.message);
	}
}

export function* update(resourceName, id, data = {}) {
	const route = yield select(
		SCHEMA_STORE_KEY,
		'getRoute',
		resourceName,
		[id]
	);

	if (!route) {
		return;
	}

	try {
		const item = yield apiFetch({
			path: route,
			method: 'POST',
			data,
			cache: 'no-store',
		});

		yield invalidateCollection(new Date().getTime());

	} catch (error) {
		NotificationManager.error(error.message);
	}

}

export function* remove(resourceName, id) {
	const ids = isArray(id) ? id : [id];
	const resource = ids.length > 1 ? `${resourceName}/bulk` : resourceName;
	const routeIds = ids.length > 1 ? [] : [1];

	if (isEmpty(ids)) {
		return;
	}

	if (!confirm(__('Are you sure you want to delete the items?'))) {
		return;
	}

	const route = yield select(
		SCHEMA_STORE_KEY,
		'getRoute',
		resource,
		routeIds
	);

	if (!route) {
		return;
	}

	console.log({
		path: route,
		method: ids.length > 1 ? 'POST' : 'DELETE',
		cache: 'no-store',
		data: ids.length > 1 ? {action: 'delete', items: ids} : null
	});

	try {
		yield apiFetch({
			path: route,
			method: ids.length > 1 ? 'POST' : 'DELETE',
			cache: 'no-store',
			data: ids.length > 1 ? {action: 'delete', items: ids} : {}
		});

		yield invalidateCollection(new Date().getTime());
		return true;
	} catch (error) {
		NotificationManager.error(error.message);
	}
}


export function receiveCollectionError(resourceName, queryString, ids, error) {
	return {
		type: 'ERROR',
		resourceName,
		queryString,
		ids,
		response: {
			items: [],
			headers: Headers,
			error,
		},
	};
}

export function receiveLastModified(timestamp) {
	return {
		type: types.RECEIVE_LAST_MODIFIED,
		timestamp,
	};
}


/**
 * Check if the store needs invalidating due to a change in last modified headers.
 *
 */
export function* invalidateCollection() {
	yield dispatch(STORE_KEY, 'invalidateResolutionForStore');
}
