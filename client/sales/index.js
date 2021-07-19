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

const Revenues = lazy( () => import( './revenues' ) );
const Customers = lazy( () => import( './customers' ) );

export default function ( props ) {
	const tabs = applyFilters( 'EACCOUNTING_SALES_TABS', [
		{
			key: 'revenues',
			container: Revenues,
			label: __( 'Revenues' ),
			capability: 'manage_options',
		},
		{
			key: 'customers',
			container: Customers,
			label: __( 'Customers' ),
			capability: 'manage_options',
		},
	] );
	return (
		<>
			<Tabs tabs={ tabs } { ...props } />
		</>
	);
}
