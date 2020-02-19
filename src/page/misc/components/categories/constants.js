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
		sortable: false,
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

export const getSearchOptions = () => [
	{
		name: 'name',
		title: __('Search'),
	},
];
