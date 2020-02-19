import {STATUS_IN_PROGRESS} from 'lib/status';
import {getDefaultTable, toFilter} from 'lib/table';
import { getFilterOptions, getDisplayGroups } from 'page/misc/components/payments/constants';
let table = getDefaultTable( [ 'name', 'number' ], toFilter( getFilterOptions(), { name: true } ), getDisplayGroups(), 'name', [ 'payments' ], 'payment', getDisplayGroups() );

export function getInitialPayments() {
	return {
		rows: [],
		saving: [],
		total: 0,
		status: STATUS_IN_PROGRESS,
		table: table,
	};
}
