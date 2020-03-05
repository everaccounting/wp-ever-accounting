/**
 * External dependencies
 */

import { translate as __ } from 'lib/locale';
export const getHeaders = () => [
	{
		name: 'paid_at',
		title: __( 'Date' ),
		sortable: true,
	},
	{
		name: 'amount',
		title: __( 'Amount' ),
		sortable: true,
	},
	{
		name: 'account_id',
		title: __( 'Account' ),
		sortable: true,
	},
	{
		name: 'type',
		title: __( 'Type' ),
		sortable: true,
	},
	{
		name: 'category_id',
		title: __( 'Category' ),
		sortable: true,
	},
	{
		name: 'reference',
		title: __( 'Reference' ),
		sortable: true,
	}
];
