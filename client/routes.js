import { applyFilters } from '@wordpress/hooks';
import Dashboard from './page/dashboard';
import Transactions from './page/transactions';
import Sales from './page/sales';
import Purchases from './page/purchases';
import Banking from './page/banking';
import Settings from './page/settings';
import Reports from "./page/reports";

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
	// {
	// 	container: Items,
	// 	path: '/items/*',
	// },
	// {
	// 	container: Items,
	// 	path: '/items',
	// },
	{
		container: Sales,
		path: '/sales/*',
	},
	{
		container: Sales,
		path: '/sales',
	},
	{
		container: Purchases,
		path: '/purchases/*',
	},
	{
		container: Purchases,
		path: '/purchases',
	},
	{
		container: Banking,
		path: '/banking/*',
	},
	{
		container: Banking,
		path: '/banking',
	},
	{
		container: Reports,
		path: '/reports/*',
	},
	{
		container: Reports,
		path: '/reports',
	},
	{
		container: Settings,
		path: '/settings/*',
	},
	{
		container: Settings,
		path: '/settings/',
	},
];

export default applyFilters(PAGES_FILTER, routes);
