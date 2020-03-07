import { STATUS_IN_PROGRESS } from 'status';
import { getDefaultTable } from '../util';

export const initialReconciliations = {
	rows: [],
	saving: [],
	total: 0,
	status: STATUS_IN_PROGRESS,
	table: getDefaultTable(['name', 'id'], ['type'], 'id'),
};

export { default as reconciliations } from './reducers';
export * from './actions';
