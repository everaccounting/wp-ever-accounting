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
		name: 'name',
		title: __('Name'),
		primary: true,
	},
	{
		name: 'type',
		title: __('Type'),
		sortable: true,
	},
	{
		name: 'color',
		title: __('Color'),
		sortable: true,
	},
	{
		name: 'actions',
		title: __('Actions'),
		sortable: false,
	}
];

export const getBulk = () => [
	{
		id: 'delete',
		name: __('Delete'),
	}
];
