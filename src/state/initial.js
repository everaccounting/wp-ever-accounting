import {getInitialAccounts} from 'state/accounts/intial';
// import {getInitialBills} from 'state/bills/intial';
// import {getInitialCategories} from 'state/categories/intial';
import {getInitialContacts} from 'state/contacts/intial';
// import {getInitialCurrencies} from 'state/currencies/intial';
// import {getInitialInvoices} from 'state/invoices/intial';
// import {getInitialPayments} from 'state/payments/intial';
import {getInitialRevenues} from 'state/revenues/intial';
// import {getInitialTaxrates} from 'state/taxrates/intial';
import {getInitialTransactions} from 'state/transactions/intial';

export function initialActions(store) {
	return store;
}

export function getInitialState() {
	return {
		accounts: getInitialAccounts(),
		// bills: getInitialBills(),
		// categories: getInitialCategories(),
		contacts: getInitialContacts(),
		// currencies: getInitialCurrencies(),
		// invoices: getInitialInvoices(),
		// payments: getInitialPayments(),
		revenues: getInitialRevenues(),
		// taxrates: getInitialTaxrates(),
		transactions: getInitialTransactions(),
	};
}
