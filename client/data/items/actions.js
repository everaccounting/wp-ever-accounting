/**
 * External dependencies
 */
import {apiFetch} from '@wordpress/data-controls';
import {select, dispatch} from "@wordpress/data"
/**
 * Internal dependencies
 */
import TYPES from './action-types';
import {NAMESPACE} from '../constants';
import {STORE_NAME} from "./constants";

export function setItems(itemType, query, items, totalCount) {
	return {
		type: TYPES.SET_ITEMS,
		items,
		itemType,
		query,
		totalCount,
	};
}

export function setError(itemType, query, error) {
	return {
		type: TYPES.SET_ERROR,
		itemType,
		query,
		error,
	};
}

export function* updateItem(itemType, updatedItem) {
	const {id} = updatedItem;
	yield setItems(itemType, id, [updatedItem], 1);
	try {
		const results = yield apiFetch({
			path: `${NAMESPACE}/${itemType}/${id}`,
			method: 'PUT',
			data: updatedItem,
		});
		return {success: true, ...results};
	} catch (error) {
		// Update failed, return back to original state.
		yield setItems(itemType, id, [updatedItem], 1);
		yield setError(id, error);
		return {success: false, ...error};
	}
}

export function* deleteItem(itemType, id) {
	try {
		const results = yield apiFetch({
			path: `${NAMESPACE}/${itemType}/${id}`,
			method: 'DELETE',
		});
		yield resetItems(itemType);
		return {success: true, ...results};
	} catch (error) {
		yield setError(id, error);
		return {success: false, ...error};
	}
}

export function* bulkAction(action, itemType, items) {
	try {
		const results = yield apiFetch( {
			path: `${ NAMESPACE }/${ itemType }/${action}`,
			method: 'PUT',
			data: items.map(item => item.id),
		} );
		return { success: true, ...results };
	} catch ( error ) {
		yield setError( itemType, error );
		return { success: false, ...error };
	}
}


/**
 * Action triggering resetting state in the store for the given selector name and
 * ResourceName
 *
 * @param {string} itemType
 */
export function* resetItems(itemType) {

	// get resolvers from core/data
	const {getCachedResolvers} = yield select('core/data');
	const resolvers = yield getCachedResolvers(STORE_NAME);

	for (const selector in resolvers) {
		if ('getItems' === selector && selectorName.indexOf(resourceName) > -1) {
			for (const entry of resolvers[selector]._map) {
				if (entry[0][0] === itemType) {
					const {invalidateResolution} = yield dispatch('core/data');
					yield invalidateResolution(STORE_NAME, selector, entry[0])
				}
			}
		}
	}
}

/**
 * Helper for determining if actions are available in the `core/data` package.
 *
 * @return {boolean}  True means additional invalidation actions available.
 */
const invalidateActionsAvailable = () => {
	return select('core/data').invalidateResolutionForStore !== undefined;
};
