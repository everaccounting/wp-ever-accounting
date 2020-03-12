import {apiFetch, select} from '@wordpress/data-controls';
import {ACTION_TYPES as types} from './action-types';

let Headers = window.Headers || null;
Headers = Headers ? new Headers() : { get: () => undefined, has: () => undefined };


export const receiveCollection = (endpoint, query, response = { items: [], headers: {} }) => {
	return {
		type: types.RECEIVE_COLLECTION,
		endpoint,
		query,
		response,
	};
};

export const resetCollection = (endpoint) => {
	return {
		type: types.RECEIVE_COLLECTION,
		endpoint
	};
};

export const receiveSelectedItem = (endpoint, id) => {
	return {
		type: types.SELECT_COLLECTION_ITEM,
		endpoint,
		id
	};
};

export const receiveSelectedItems = (endpoint, onoff) => {
	console.group("receiveSelectedItems");
	console.log(endpoint);
	console.log(onoff);
	console.groupEnd();
};

export const receiveUpdatedItem = (endpoint, item) =>{
	console.group("receiveUpdatedItem");
	console.log(endpoint);
	console.log(item);
	console.groupEnd();
};

export const receiveBulkAction = (endpoint, action, ids) =>{
	console.group("receiveBulkAction");
	console.log(endpoint);
	console.log(action);
	console.log(ids);
	console.groupEnd();
};

export const receiveCollectionError = (endpoint, queryString, error) =>{
	return {
		type: 'FAILED',
		endpoint,
		queryString,
		response: {
			items: [],
			meta: {},
			error,
		},
	};
};
