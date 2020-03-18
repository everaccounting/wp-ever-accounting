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
		name: 'code',
		title: __('Code'),
		sortable: true,
	},
	{
		name: 'rate',
		title: __('Rate'),
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
