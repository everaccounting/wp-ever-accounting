/**
 * External dependencies
 */

import { translate as __ } from 'lib/locale';

export const getHeaders = () => [
	{
		name: 'cb',
		check: true,
	},
	{
		name: 'created_date',
		title: __('Created Date'),
		primary: true,
		sortable: true,
	},
	{
		name: 'account',
		title: __('Account'),
		sortable: true,
	},
	{
		name: 'period',
		title: __('Period'),
		sortable: false,
	},
	{
		name: 'closing_balance',
		title: __('Closing Balance'),
		sortable: true,
	},
	{
		name: 'status',
		title: __('Status'),
		sortable: true,
	},
	{
		name: 'actions',
		title: __('Actions'),
		sortable: false,
	},
];

export const getBulk = () => [
	{
		id: 'delete',
		name: __('Delete'),
	}
];
