import { lazy } from '@wordpress/element';

// import Dashboard from './page/dashboard';
// import Transactions from './page/transactions';
// import Incomes from './page/incomes';
// import Expenses from './page/expenses';
import { applyFilters } from '@wordpress/hooks';
// import Contacts from './page/contacts';
// import Items from './page/items';
// import Banking from './page/banking';
import Misc from './page/misc';
// const Misc = lazy( () => import( './page/misc' ) );

export const PAGES_FILTER = 'eaccounting_admin_pages_list';

export const routes = [
	// {
	// 	container: Dashboard,
	// 	path: '/',
	// },
	// {
	// 	container: Transactions,
	// 	path: '/transactions',
	// },
	// {
	// 	container: Items,
	// 	path: '/items',
	// },
	// {
	// 	container: Contacts,
	// 	path: '/contacts',
	// },
	// {
	// 	container: Incomes,
	// 	path: '/incomes/:tab/add',
	// },
	// {
	// 	container: Incomes,
	// 	path: '/incomes/:tab/:id',
	// },
	// {
	// 	container: Incomes,
	// 	path: '/incomes/:tab',
	// },
	// {
	// 	container: Incomes,
	// 	path: '/incomes/',
	// },
	// {
	// 	container: Expenses,
	// 	path: '/expenses/:tab/add',
	// },
	// {
	// 	container: Expenses,
	// 	path: '/expenses/:tab/:id',
	// },
	// {
	// 	container: Expenses,
	// 	path: '/expenses/:tab',
	// },
	// {
	// 	container: Expenses,
	// 	path: '/expenses/',
	// },
	// {
	// 	container: Banking,
	// 	path: '/banking/:tab/add',
	// },
	// {
	// 	container: Banking,
	// 	path: '/banking/:tab/:id',
	// },
	// {
	// 	container: Banking,
	// 	path: '/banking/:tab',
	// },
	// {
	// 	container: Banking,
	// 	path: '/banking/',
	// },
	{
		container: Misc,
		path: '/misc/:tab',
	},
	{
		container: Misc,
		path: '/misc/',
	},
];

export default applyFilters(PAGES_FILTER, routes);
