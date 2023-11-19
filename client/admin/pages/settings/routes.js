/**
 * WordPress dependencies
 */
import { applyFilters } from '@wordpress/hooks';
import { Spinner } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import { lazy, Suspense } from '@wordpress/element';

/**
 * External dependencies
 */
import { Navigate, useRoutes } from 'react-router-dom';
const General = lazy( () => import( './general' ) );
const Taxes = lazy( () => import( './taxes' ) );
const Customization = lazy( () => import( './customization' ) );
const Categories = lazy( () => import( './categories' ) );
const Currencies = lazy( () => import( './currencies' ) );

export function getRoutes() {
	return applyFilters( 'ever_accounting_settings_routes', [
		{
			path: 'general',
			name: __( 'General', 'wp-ever-accounting' ),
			element: <General />,
			icon: 'admin-generic',
		},
		{
			path: 'taxes',
			name: __( 'Taxes', 'wp-ever-accounting' ),
			element: <Taxes />,
			icon: 'admin-generic',
		},
		{
			path: 'customization',
			name: __( 'Customization', 'wp-ever-accounting' ),
			element: <Customization />,
			icon: 'admin-generic',
		},
		{
			path: 'categories',
			name: __( 'Categories', 'wp-ever-accounting' ),
			element: <Categories />,
		},
		{
			path: 'currencies',
			name: __( 'Currencies', 'wp-ever-accounting' ),
			element: <Currencies />,
		},
	] );
}

export function Routes() {
	const routes = getRoutes();
	// push the default route to the end of the array to make sure it is the last route.
	routes.push( {
		path: '*',
		element: <Navigate to="general" />,
	} );
	const router = useRoutes( routes );
	return <Suspense fallback={ <Spinner /> }>{ router }</Suspense>;
}
