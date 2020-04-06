/**
 * External dependencies
 */

import {__} from '@wordpress/i18n';

export const getHeaders = () => [
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
		name: 'customer',
		title: __('Customer'),
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
	},
];
