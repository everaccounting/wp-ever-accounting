/**
 * External dependencies
 */

import {translate as __} from 'lib/locale';

export const getHeaders = () => [
	{
		name: 'cb',
		check: true,
	},
	{
		name: 'paid_at',
		title: __('Date'),
		primary: true,
		sortable: true,
	},
	{
		name: 'amount',
		title: __('Amount'),
		sortable: true,
	},
	{
		name: 'vendor',
		title: __('Vendor'),
		sortable: true,
	},
	{
		name: 'category',
		title: __('Category'),
		sortable: true,
	},
	{
		name: 'account',
		title: __('Account'),
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
