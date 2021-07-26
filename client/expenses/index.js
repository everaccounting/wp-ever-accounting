/**
 * WordPress dependencies
 */
import { applyFilters } from '@wordpress/hooks';
import { __ } from '@wordpress/i18n';
import { lazy } from '@wordpress/element';
/**
 * External dependencies
 */
import { Tabs } from '@eaccounting/components';

const Payments = lazy( () => import( './payments' ) );
const Vendors = lazy( () => import( './vendors' ) );
const Bills = lazy( () => import( './bills' ) );

export default function ( props ) {
	const tabs = applyFilters( 'EACCOUNTING_EXPENSES_TABS', [
		{
			key: 'payments',
			container: Payments,
			label: __( 'Payments' ),
			capability: 'manage_options',
		},
		{
			key: 'bills',
			container: Bills,
			label: __( 'Bills' ),
			capability: 'manage_options',
		},
		{
			key: 'vendors',
			container: Vendors,
			label: __( 'Vendors' ),
			capability: 'manage_options',
		},
	] );
	return (
		<>
			<Tabs tabs={ tabs } { ...props } />
		</>
	);
}
