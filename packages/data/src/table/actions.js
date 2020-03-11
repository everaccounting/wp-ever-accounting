import {apiFetch, select} from '@wordpress/data-controls';
import {ACTION_TYPES as types} from './action-types';

let Headers = window.Headers || null;
Headers = Headers ? new Headers() : { get: () => undefined, has: () => undefined };


export const receiveCollection = (endpoint, query, response = { items: [], total: 0 },) => {
	console.group("receiveCollection");
	console.log(endpoint);
	console.log(query);
	console.groupEnd();
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

export const receiveSelectedItem = (endpoint, item) => {
	console.group("receiveSelectedItem");
	console.log(endpoint);
	console.log(item);
	console.groupEnd();
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
	console.group("receiveCollectionError");
	console.log(endpoint);
	console.log(queryString);
	console.log(error);
	console.groupEnd();
	return {
		type: 'ERROR',
		endpoint,
		queryString,
		response: {
			items: [],
			headers: Headers,
			error,
		},
	};
};
