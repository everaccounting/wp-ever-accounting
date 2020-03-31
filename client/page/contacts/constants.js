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
		name: 'email',
		title: __('Email'),
		sortable: false,
	},
	{
		name: 'phone',
		title: __('Phone'),
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
