import { STATUS_IN_PROGRESS } from 'status';
import { getDefaultTable } from '../util';

export const initialContacts = {
	rows: [],
	saving: [],
	total: 0,
	status: STATUS_IN_PROGRESS,
	table: getDefaultTable(['first_name', 'id'], ['type'], 'id'),
};

export { default as contacts } from './reducers';
export * from './actions';
