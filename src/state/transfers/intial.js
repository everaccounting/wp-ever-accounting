import {STATUS_IN_PROGRESS} from 'lib/status';
import {getDefaultTable} from 'lib/table';
let table = getDefaultTable( [ 'name', 'type' ], ['type'], 'name');
export function getInitialContacts() {
	return {
		rows: [],
		saving: [],
		total: 0,
		status: STATUS_IN_PROGRESS,
		table: table,
	};
}

export const initialContact = {
	name: '',
	number: '',
	opening_balance: '0',
	bank_name: '',
	bank_phone: '',
	bank_address: '',
	currency: eAccountingi10n.default_currency,
	enabled: true,
};
