import Dashboard from "./page/dashboard";
import Transactions from "./page/transactions";
import Incomes from "./page/incomes";
import {applyFilters} from "@wordpress/hooks";

export const PAGES_FILTER = 'eaccounting_admin_pages_list';

export const routes = [
	{
		container: Dashboard,
		path: '/',
	},
	{
		container: Transactions,
		path: '/transactions',
	},
	{
		container: Incomes,
		path: '/incomes/:tab/add',
	},
	{
		container: Incomes,
		path: '/incomes/:tab/:id(\d+)',
	},
	{
		container: Incomes,
		path: '/incomes/:tab',
	},
	{
		container: Incomes,
		path: '/incomes/',
	},
];

export default applyFilters(PAGES_FILTER, routes);
