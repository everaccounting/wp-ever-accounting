import {STATUS_IN_PROGRESS} from 'lib/status';
export function getInitialTransactions() {
	return {
		rows: [],
		saving: [],
		total: 0,
		status: STATUS_IN_PROGRESS,
		table: {
			orderby: '',
			order: '',
			page: 1,
			per_page: parseInt(eAccountingi10n.per_page, 10),
			filterBy: {},
			selected: []
		},
	};
}
