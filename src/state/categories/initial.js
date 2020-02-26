import {STATUS_IN_PROGRESS} from 'lib/status';
import {getDefaultTable} from 'lib/table';
let table = getDefaultTable( [ 'name', 'type' ], ['type'], 'name');

export function getInitialCategories() {
	return {
		rows: [],
		saving: [],
		total: 0,
		status: STATUS_IN_PROGRESS,
		table: table,
	};
}
