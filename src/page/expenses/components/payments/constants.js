/**
 * External dependencies
 */

import {translate as __} from 'lib/locale';

export const getDisplayOptions = () => [
	{value: 'name', label: __('Name')},
	{value: 'type', label: __('Type')},
	{value: 'color', label: __('Color')},
	{value: 'status', label: __('Status')},
];

export const getDisplayGroups = () => [];

export const getFilterOptions = () => [];

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
		name: 'amount',
		title: __('Amount'),
		sortable: true,
	},
	{
		name: 'vendor',
		title: __('Vendor'),
		sortable: true,
	},
	{
		name: 'category',
		title: __('Category'),
		sortable: true,
	},
	{
		name: 'account',
		title: __('Account'),
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
