import { STATUS_IN_PROGRESS } from 'status';
import { getDefaultTable } from 'lib/table';
let table = getDefaultTable(['name', 'rate', 'type', 'status'], ['type'], 'name');
import { translate as __ } from 'lib/locale';

export function getInitialTaxRates() {
	return {
		rows: [],
		saving: [],
		total: 0,
		status: STATUS_IN_PROGRESS,
		table: table,
	};
}

export const taxTypes = [
	{
		label: __('Normal'),
		value: 'normal',
	},
	{
		label: __('Inclusive'),
		value: 'inclusive',
	},
	{
		label: __('Compound'),
		value: 'compound',
	},
];
