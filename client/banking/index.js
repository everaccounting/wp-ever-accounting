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

const AccountsTab = lazy( () => import( './accounts' ) );
const Transfers = lazy( () => import( './transfers' ) );

export default function ( props ) {
	const tabs = applyFilters( 'EACCOUNTING_BANKING_TABS', [
		{
			key: 'accounts',
			container: AccountsTab,
			label: __( 'Accounts' ),
			capability: 'manage_options',
		},
		{
			key: 'transfers',
			container: Transfers,
			label: __( 'Transfers' ),
			capability: 'manage_options',
		},
	] );
	return (
		<>
			<Tabs tabs={ tabs } { ...props } />
		</>
	);
}
