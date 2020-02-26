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
		name: 'rate',
		title: __('Rate %'),
		sortable: true,
	},
	{
		name: 'type',
		title: __('Type'),
		sortable: true,
	},
	{
		name: 'status',
		title: __('Status'),
		sortable: true,
	}
];

export const getBulk = () => [
	{
		id: 'delete',
		name: __('Delete'),
	},
	{
		id: 'enable',
		name: __('Enable'),
	},
	{
		id: 'disable',
		name: __('Disable'),
	},
];
