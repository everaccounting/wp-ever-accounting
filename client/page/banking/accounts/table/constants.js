/**
 * External dependencies
 */

import { __ } from '@wordpress/i18n';

export const getHeaders = () => [
	{
		name: 'cb',
		check: true,
	},
	{
		name: 'name',
		title: __('Name'),
		primary: true,
	},
	{
		name: 'balance',
		title: __('Balance'),
		sortable: false,
	},
	{
		name: 'number',
		title: __('Number'),
		sortable: true,
	},
	{
		name: 'bank_name',
		title: __('Bank Name'),
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
	{
		id: 'export',
		name: __('Export'),
	},
];
