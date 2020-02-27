import {STATUS_IN_PROGRESS} from 'lib/status';
import {getDefaultTable} from 'lib/table';
import {translate as __} from 'lib/locale';
let table = getDefaultTable(['name', 'type'], ['type'], 'name');

export function getInitialCategories() {
	return {
		rows: [],
		saving: [],
		total: 0,
		status: STATUS_IN_PROGRESS,
		table: table,
		undefined: undefined,
		test: 1,
	};
}

export const categoryTypes = [
	{
		label: __('Expense'),
		value: 'expense',
	},
	{
		label: __('Income'),
		value: 'income',
	},
	{
		label: __('Item'),
		value: 'item',
	},
	{
		label: __('Other'),
		value: 'other',
	}
];
