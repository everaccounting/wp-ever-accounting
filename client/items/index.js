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

const Items = lazy( () => import( './items' ) );

export default function ( props ) {
	const tabs = applyFilters( 'EACCOUNTING_ITEMS_TABS', [
		{
			key: 'items',
			container: Items,
			label: __( 'Items' ),
			capability: 'manage_options',
		},
	] );
	return (
		<>
			<Tabs tabs={ tabs } { ...props } />
		</>
	);
}
