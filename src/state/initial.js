import {getInitialCurrencies} from 'state/currencies/initial';
import {getInitialTaxRates} from 'state/taxrates/initial';
import {getInitialCategories} from 'state/categories/initial';
// import {getInitialAccounts} from 'state/accounts/intial';
// import {getInitialBills} from 'state/bills/intial';
// import {getInitialContacts} from 'state/contacts/intial';
// import {getInitialInvoices} from 'state/invoices/intial';
// import {getInitialPayments} from 'state/payments/intial';
// import {getInitialRevenues} from 'state/revenues/intial';
import {getInitialTransactions} from 'state/transactions/initial';

export function initialActions(store) {
	return store;
}

export function getInitialState() {
	return {
		// accounts: getInitialAccounts(),
		// bills: getInitialBills(),
		categories: getInitialCategories(),
		// contacts: getInitialContacts(),
		currencies: getInitialCurrencies(),
		// invoices: getInitialInvoices(),
		// payments: getInitialPayments(),
		// revenues: getInitialRevenues(),
		taxrates: getInitialTaxRates(),
		transactions: getInitialTransactions(),
	};
}
