import {ACTION_TYPES as types} from './action-types.js';

export const setSelected = (resourceName, id) => {
	return {
		types: types.RECEIVE_SELECTED,
		resourceName,
		id
	}
};

export const setAllSelected = (resourceName, items, onoff) => {
	return {
		types: types.RECEIVE_ALL_SELECTED,
		resourceName,
		items,
		onoff
	}
};

export const setTotal = (resourceName, total) => {
	return {
		types: types.RECEIVE_TOTAL,
		resourceName,
		total
	}
};
