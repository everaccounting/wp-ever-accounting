import {STATUS_IN_PROGRESS} from 'lib/status';
import {getDefaultTable, toFilter} from 'lib/table';
import { getFilterOptions, getDisplayGroups } from 'page/incomes/components/revenues/constants';
let table = getDefaultTable( [ 'name', 'number' ], toFilter( getFilterOptions(), { name: true } ), getDisplayGroups(), 'name', [ 'revenues' ], 'revenue', getDisplayGroups() );

export function getInitialRevenues() {
	return {
		rows: [],
		saving: [],
		total: 0,
		status: STATUS_IN_PROGRESS,
		table: table,
	};
}
