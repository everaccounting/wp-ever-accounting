/**
 * External dependencies
 */

/**
 * Internal dependencies
 */
import TYPES from './action-types';

export function setItems( itemType, query, items, totalCount ) {
	return {
		type: TYPES.SET_ITEMS,
		items,
		itemType,
		query,
		totalCount,
	};
}

export function setError( itemType, query, error ) {
	return {
		type: TYPES.SET_ERROR,
		itemType,
		query,
		error,
	};
}