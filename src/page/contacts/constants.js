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
