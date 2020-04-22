/**
 * Internal dependencies
 */
import { ACTION_TYPES as types } from './action-types';
import { fetch, fetchFromAPIWithTotal } from '../base-controls';
/**
 * WordPress dependencies
 */
import { addQueryArgs } from '@wordpress/url';

/**
 *
 * @param type
 * @param payload
 * @return {{payload: {}, type: *}}
 */
function receiveTableUpdate(type, payload = {}) {
	return {
		type,
		payload,
	};
}

/**
 *
 * @param endpoint
 * @param query
 * @param base
 * @return {Generator<{path: string, type: string}|{payload: {}, type: *}, void, ?>}
 */
export function* setTable(endpoint, query = null, base = '/ea/v1/') {
	yield receiveTableUpdate(types.TABLE_LOADING);
	const path = addQueryArgs(base + endpoint, query);
	try {
		const payload = yield fetchFromAPIWithTotal(path);
		yield receiveTableUpdate(types.TABLE_LOADED, payload);
	} catch (e) {
		yield receiveTableUpdate(types.TABLE_FAILED);
	}
}

/**
 * set
 *
 * @param id
 */
export function setTableSelected(id) {
	return {
		type: types.TABLE_ITEM_SELECTED,
		payload: { id },
	};
}

export function setTableAllSelected(onoff) {
	return {
		type: types.TABLE_ALL_SELECTED,
		payload: { onoff },
	};
}
