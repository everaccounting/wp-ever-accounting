import {ACTION_TYPES as types} from './action-types';
import {TABLE_STORE} from "./index";
import { dispatch } from '@wordpress/data-controls';
export const receiveResponse = (items = [], headers={}) =>{
	return {
		type:types.TABLE_ITEMS_LOADED,
		items,
		headers
	}
};

export const receiveError = (endpoint, query, error) =>{
	return {
		type:types.TABLE_FAILED,
		items,
		headers
	}
};

