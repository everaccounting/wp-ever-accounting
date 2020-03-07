import { STATUS_IN_PROGRESS } from 'status';
import { getDefaultTable } from '../util';

export const initialTransactions = {
	rows: [],
	saving: [],
	total: 0,
	status: STATUS_IN_PROGRESS,
	table: getDefaultTable(['name', 'paid_at'], ['type'], 'paid_at'),
};

export { default as transactions } from './reducers';
export * from './actions';
