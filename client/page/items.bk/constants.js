/**
 * External dependencies
 */

import {__} from '@wordpress/i18n';

export const getHeaders = () => [
	{
		name: 'photo',
		title: __('Photo'),
		primary: true,
		sortable: false,
	},
	{
		name: 'name',
		title: __('Name'),
		sortable: true,
	},
	{
		name: 'category',
		title: __('Category'),
		sortable: true,
	},
	{
		name: 'quantity',
		title: __('Quantity'),
		sortable: true,
	},
	{
		name: 'sale_price',
		title: __('Sale Price'),
		sortable: true,
	},
	{
		name: 'purchase_price',
		title: __('Purchase Price'),
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
