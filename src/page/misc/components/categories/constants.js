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
	}
];

export const getBulk = () => [
	{
		id: 'delete',
		name: __('Delete'),
	}
];
