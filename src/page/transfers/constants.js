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
		name: 'date',
		title: __('Date'),
		primary: true,
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
		name: 'amount',
		title: __('Amount'),
		sortable: true,
	},
	{
		name: 'actions',
		title: __('Actions')
	},
];

export const getBulk = () => [
	{
		id: 'delete',
		name: __('Delete'),
	}
];
