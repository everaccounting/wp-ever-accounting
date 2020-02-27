/**
 * External dependencies
 */
import {combineReducers} from 'redux';

// import transactions from 'state/transactions/reducer';
// import contacts from 'state/contacts/reducer';
// import invoices from 'state/invoices/reducer';
// import revenues from 'state/revenues/reducer';
// import bills from 'state/bills/reducer';
// import payments from 'state/payments/reducer';
// import accounts from 'state/accounts/reducer';
// import transactions from 'state/transactions/reducer';
import categories from 'state/categories/reducer';
import currencies from 'state/currencies/reducer';
import taxrates from 'state/taxrates/reducer';
const reducer = combineReducers({
	// accounts,
	// bills,
	categories,
	currencies,
	taxrates,
	// contacts,
	// invoices,
	// revenues,
	// transactions,
	// payments,
	// currencies,
});

export default reducer;
