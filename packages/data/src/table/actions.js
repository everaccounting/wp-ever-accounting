import {ACTION_TYPES as types} from './action-types';
import {TABLE_STORE_KEY} from "./index";
import {dispatch} from '@wordpress/data-controls';
import {fetchFromAPI} from './controls';

export const receiveResponse = (items = [], headers = {}) => {
	return {
		type: types.TABLE_ITEMS_LOADED,
		items,
		headers
	}
};

export const receiveError = (endpoint, query, error) => {
	return {
		type: types.TABLE_FAILED,
		items,
		headers
	}
};

export const setLoading = () => {
	return {
		type: types.TABLE_LOADING,
		items,
		headers
	}
};

export function* loadItems(enpoint, query){
	const response = yield fetchFromAPI(enpoint, query);
	const {items = [], headers = {}} = response;
	yield receiveResponse(items, headers);
}
