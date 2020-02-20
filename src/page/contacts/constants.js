/**
 * External dependencies
 */

import { translate as __ } from 'lib/locale';

export const getDisplayOptions = () => [
	{ value: 'name', label: __( 'Name' ) },
	{ value: 'number', label: __( 'Number' ) },
	{ value: 'balance', label: __( 'Balance' ) },
	{ value: 'status', label: __( 'Status' ) },
];

export const getDisplayGroups = () => [
	{
		value: 'standard',
		label: __( 'Standard Display' ),
		grouping: [ 'name', 'email', 'phone', 'status' ],
	},
	{
		value: 'minimal',
		label: __( 'Compact Display' ),
		grouping: [ 'name', 'email', 'phone' ],
	},
];

export const getFilterOptions = () => [
	{
		label: __( 'Status' ),
		value: 'status',
		options: [
			{
				label: __( 'Enabled' ),
				value: 'enabled',
			},
			{
				label: __( 'Disabled' ),
				value: 'disabled',
			},
		],
	}
];

export const getHeaders = () => [
	{
		name: 'cb',
		check: true,
	},
	{
		name: 'name',
		title: __( 'Name' ),
		primary: true,
	},
	{
		name: 'email',
		title: __( 'Email' ),
		sortable: false,
	},
	{
		name: 'phone',
		title: __( 'Phone' ),
		sortable: true,
	},
	{
		name: 'status',
		title: __( 'Status' ),
		sortable: true,
	}
];

export const getBulk = () => [
	{
		id: 'delete',
		name: __( 'Delete' ),
	},
	{
		id: 'enable',
		name: __( 'Enable' ),
	},
	{
		id: 'disable',
		name: __( 'Disable' ),
	},
];

export const getSearchOptions = () => [
	{
		name: 'name',
		title: __( 'Search' ),
	},
];
