import { combineReducers } from 'redux';
import { transactions } from './transactions';
import { contacts } from './contacts';
import { revenues } from './revenues';
import { revenue } from './revenue';
import { categories } from './categories';
import { accounts } from './accounts';
import { payments } from './payments';
import { currencies } from './currencies';
import { taxrates } from './taxrates';

const createRootReducer = combineReducers({
	transactions,
	contacts,
	revenues,
	revenue,
	categories,
	accounts,
	payments,
	currencies,
	taxrates,
});

export default createRootReducer;
