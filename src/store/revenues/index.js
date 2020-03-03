import {STATUS_IN_PROGRESS} from 'status';
import {getDefaultTable} from "../util";

export const initialRevenues = {
	rows: [],
	saving: [],
	total: 0,
	status: STATUS_IN_PROGRESS,
	table: getDefaultTable(['name', 'paid_at'], ['type'], 'paid_at'),
};

export {default as revenues} from './reducers';
export * from './actions';
