import {STATUS_IN_PROGRESS} from 'status';
import {getDefaultTable} from 'lib/table';
let table = getDefaultTable(['name', 'number', 'current_balance'], ['type'], 'name');

export function getInitialAccounts() {
	return {
		rows: [],
		saving: [],
		total: 0,
		status: STATUS_IN_PROGRESS,
		table: table,
	};
}

export const initialAccount = {
	name: '',
	number: '',
	opening_balance: '0',
	bank_name: '',
	bank_phone: '',
	bank_address: '',
	currency: eAccountingi10n.default_currency,
};
