import { applyFilters } from '@wordpress/hooks';
import { lazy } from '@wordpress/element';

const Dashboard = lazy( () => import( './page/dashboard' ) );
const Transactions = lazy( () => import( './page/transactions' ) );
const Sales = lazy( () => import( './page/sales' ) );
const Purchases = lazy( () => import( './page/purchases' ) );
const Banking = lazy( () => import( './page/banking' ) );
const Reports = lazy( () => import( './page/reports' ) );
const Settings = lazy( () => import( './page/settings' ) );

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
