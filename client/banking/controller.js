/**
 * WordPress dependencies
 */
import { applyFilters } from '@wordpress/hooks';
import { __ } from '@wordpress/i18n';
import { lazy } from '@wordpress/element';

/**
 * Internal dependencies
 */
const Accounts = lazy( () => import( './tabs/accounts' ) );
const Transactions = lazy( () => import( './tabs/transactions' ) );
const Transfers = lazy( () => import( './tabs/transfers' ) );

export const getTabs = () => {
	const tabs = [
		{
			key: 'accounts',
			container: Accounts,
			label: __( 'Accounts' ),
			capability: 'manage_options',
		},
		{
			key: 'transactions',
			container: Transactions,
			label: __( 'Transactions' ),
			capability: 'manage_options',
		},
		{
			key: 'transfers',
			container: Transfers,
			label: __( 'Transfers' ),
			capability: 'manage_options',
		},
	];

	return applyFilters( 'eaccounting_banking_tabs', tabs );
};
