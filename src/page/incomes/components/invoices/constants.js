/**
 * External dependencies
 */

import { translate as __ } from 'lib/locale';

export const getDisplayOptions = () => [
	{ value: 'name', label: __('Name') },
	{ value: 'type', label: __('Type') },
	{ value: 'color', label: __('Color') },
	{ value: 'status', label: __('Status') },
];

export const getDisplayGroups = () => [];

export const getFilterOptions = () => [];

export const getHeaders = () => [
	{
		name: 'cb',
		check: true,
	},
	{
		name: 'number',
		title: __('Number'),
		primary: true,
		sortable: true,
	},
	{
		name: 'customer',
		title: __('Customer'),
		sortable: true,
	},
	{
		name: 'amount',
		title: __('Amount'),
		sortable: true,
	},
	{
		name: 'invoice_date',
		title: __('Invoice Date'),
		sortable: true,
	},
	{
		name: 'due_date',
		title: __('Due Date'),
		sortable: true,
	},
	{
		name: 'status',
		title: __('Status'),
		sortable: true,
	},
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

export const getSearchOptions = () => [
	{
		name: 'name',
		title: __('Search'),
	},
];
