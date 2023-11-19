/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { lazy, cloneElement, Suspense } from '@wordpress/element';
import { Spinner } from '@wordpress/components';
import { applyFilters } from '@wordpress/hooks';

/**
 * External dependencies
 */
import { Navigate, useRoutes, useLocation } from 'react-router-dom';
import { useUser } from '@eac/data';

const Dashboard = lazy( () => import( './pages/dashboard' ) );

// Items Pages.
const Items = lazy( () => import( './pages/items' ) );
const ItemsList = lazy( () => import( './pages/items/items' ) );

// Sales Pages.
const Sales = lazy( () => import( './pages/sales' ) );
const Revenues = lazy( () => import( './pages/sales/revenues' ) );
const Invoices = lazy( () => import( './pages/sales/invoices' ) );
const Customers = lazy( () => import( './pages/sales/customers' ) );

//purchase pages.
const Purchases = lazy( () => import( './pages/purchases' ) );
const Expenses = lazy( () => import( './pages/purchases/expenses' ) );
const Bills = lazy( () => import( './pages/purchases/bills' ) );
const Vendors = lazy( () => import( './pages/purchases/vendors' ) );

// Banking Pages.
const Banking = lazy( () => import( './pages/banking' ) );
const Accounts = lazy( () => import( './pages/banking/accounts' ) );
const Transfers = lazy( () => import( './pages/banking/transfers' ) );
const Currencies = lazy( () => import( './pages/banking/currencies' ) );

const Reports = lazy( () => import( './pages/reports' ) );
const Tools = lazy( () => import( './pages/tools' ) );
const Settings = lazy( () => import( './pages/settings' ) );
const Addons = lazy( () => import( './pages/addons' ) );
const Help = lazy( () => import( './pages/help' ) );

export const getRoutes = () =>
	applyFilters( 'wp_ever_accounting_routes', [
		{
			path: '/',
			name: __( 'Dashboard', 'wp-ever-accounting' ),
			element: <Dashboard />,
			capability: 'manage_options',
		},
		{
			path: '/items',
			name: __( 'Items', 'wp-ever-accounting' ),
			element: <Items />,
			children: [
				{
					path: 'items/*',
					name: __( 'Items', 'wp-ever-accounting' ),
					tab: true,
					element: <ItemsList />,
				},
			],
		},
		{
			path: '/sales',
			name: __( 'Sales', 'wp-ever-accounting' ),
			element: <Sales />,
			children: [
				{
					path: 'revenues/*',
					name: __( 'Revenues', 'wp-ever-accounting' ),
					tab: true,
					element: <Revenues />,
				},
				{
					path: 'invoices/*',
					name: __( 'Invoices', 'wp-ever-accounting' ),
					tab: true,
					element: <Invoices />,
				},
				{
					path: 'customers/*',
					name: __( 'Customers', 'wp-ever-accounting' ),
					tab: true,
					element: <Customers />,
				},
			],
		},
		{
			path: '/purchases',
			name: __( 'Purchases', 'wp-ever-accounting' ),
			element: <Purchases />,
			children: [
				{
					path: 'expenses/*',
					name: __( 'Expenses', 'wp-ever-accounting' ),
					tab: true,
					element: <Expenses />,
				},
				{
					path: 'bills/*',
					name: __( 'Bills', 'wp-ever-accounting' ),
					tab: true,
					element: <Bills />,
				},
				{
					path: 'vendors/*',
					name: __( 'Vendors', 'wp-ever-accounting' ),
					tab: true,
					element: <Vendors />,
				},
			],
		},
		{
			path: '/banking',
			name: __( 'Banking', 'wp-ever-accounting' ),
			element: <Banking />,
			children: [
				{
					path: 'accounts/*',
					name: __( 'Accounts', 'wp-ever-accounting' ),
					tab: true,
					element: <Accounts />,
				},
				{
					path: 'transfers/*',
					name: __( 'Transfers', 'wp-ever-accounting' ),
					tab: true,
					element: <Transfers />,
				},
				{
					path: 'currencies/*',
					name: __( 'Currencies', 'wp-ever-accounting' ),
					tab: true,
					element: <Currencies />,
				},
			],
		},
		{
			path: '/reports',
			name: __( 'Reports', 'wp-ever-accounting' ),
			element: <Reports />,
			capability: 'manage_options',
		},
		{
			path: '/tools',
			name: __( 'Tools', 'wp-ever-accounting' ),
			element: <Tools />,
			capability: 'manage_options',
		},
		{
			path: '/settings/*',
			name: __( 'Settings', 'wp-ever-accounting' ),
			element: <Settings />,
			capability: 'manage_options',
		},
		{
			path: '/addons/*',
			name: __( 'Addons', 'wp-ever-accounting' ),
			element: <Addons />,
			capability: 'manage_options',
		},
		{
			path: '/help',
			name: __( 'Help', 'wp-ever-accounting' ),
			element: <Help />,
			capability: 'manage_options',
		},
		{
			path: '*',
			element: <Navigate to="/" />,
		},
	] );

export function Routes() {
	const location = useLocation();
	const { userCan, userData } = useUser();
	const routes = getRoutes()
		.map( ( route ) => {
			// if element exists, pass route to element as prop.
			if ( route.element ) {
				route = {
					...route,
					element: cloneElement( route.element, {
						routes: route,
						userData,
						location,
					} ),
				};
			}
			return route;
		} )
		.map( ( route ) => {
			// if the route contains children, add a blank route to the top of the children array to act as a default route.
			if ( route.children && route.children[ 0 ].path && route.children[ 0 ].path !== '' ) {
				route.children.unshift( {
					path: '',
					element: (
						<Navigate
							to={ route.children[ 0 ].path.replace( /[^a-zA-Z0-9#\/]/g, '' ).replace( /\/$/, '' ) }
						/>
					),
				} );
			}

			return route;
		} );
	// .filter( ( route ) => {
	// 	// if the route has a capability, check if the user has that capability.
	// 	if ( route.capability ) {
	// 		return userCan( route.capability );
	// 	}
	// 	return true;
	// } );

	const router = useRoutes( routes );
	return <Suspense fallback={ <Spinner /> }>{ router }</Suspense>;
}
