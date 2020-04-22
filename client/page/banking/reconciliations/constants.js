/**
 * External dependencies
 */

import { __ } from '@wordpress/i18n';

export const getHeaders = () => [
	{
		name: 'transferred_at',
		title: __('Created Date'),
		primary: true,
		sortable: true,
	},
	{
		name: 'amount',
		title: __('Amount'),
		sortable: true,
	},
	{
		name: 'from_account',
		title: __('From Account'),
		sortable: true,
	},
	{
		name: 'to_account',
		title: __('To Account'),
		sortable: true,
	},
	{
		name: 'actions',
		title: __('Actions'),
	},
];

export const getBulk = () => [
	{
		id: 'delete',
		name: __('Delete'),
	},
];
