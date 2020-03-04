import {STATUS_IN_PROGRESS} from 'status';
import {getDefaultTable} from "../util";

export const initialCategories = {
	rows: [],
	saving: [],
	total: 0,
	status: STATUS_IN_PROGRESS,
	table: getDefaultTable(['name', 'id'], ['type'], 'id'),
};


export {default as categories} from './reducers';
export * from './actions';
