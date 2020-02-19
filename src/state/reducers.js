/**
 * External dependencies
 */
import {combineReducers} from 'redux';

// import transactions from 'state/transactions/reducer';
import contacts from 'state/contacts/reducer';
// import invoices from 'state/invoices/reducer';
// import revenues from 'state/revenues/reducer';
// import bills from 'state/bills/reducer';
// import payments from 'state/payments/reducer';
import accounts from 'state/accounts/reducer';
// import categories from 'state/categories/reducer';
// import currencies from 'state/currencies/reducer';
// import taxrates from 'state/taxrates/reducer';
import transactions from 'state/transactions/reducer';

const reducer = combineReducers({
	accounts,
	// bills,
	// categories,
	contacts,
	// invoices,
	// revenues,
	// taxrates,
	transactions,
	// payments,
	// currencies,
});

export default reducer;
