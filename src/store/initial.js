import {initialRevenues} from './revenues';
import {initialRevenue} from './revenue';
import {initialTransactions} from './transactions';
import {initialContacts} from './contacts';
import {initialCategories} from './categories';
import {initialAccounts} from './accounts';
import {initialPayments} from './payments';
import {initialCurrencies} from './currencies';
import {initialTaxRates} from './taxrates';
import {initialTransfers} from "./transfers";
import {initialReconciliations} from "./reconciliations";

export default function getInitialState() {
	return {
		contacts: initialContacts,
		transactions: initialTransactions,
		revenues: initialRevenues,
		revenue: initialRevenue,
		accounts: initialAccounts,
		transfers: initialTransfers,
		currencies: initialCurrencies,
		taxrates: initialTaxRates,
		categories: initialCategories,
		payments: initialPayments,
		reconciliations: initialReconciliations,
	};
}
