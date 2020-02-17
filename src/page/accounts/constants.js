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
		grouping: [ 'name', 'number', 'balance', 'bank_name', 'status' ],
	},
	{
		value: 'minimal',
		label: __( 'Compact Display' ),
		grouping: [ 'name', 'balance', 'status' ],
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
		name: 'balance',
		title: __( 'Balance' ),
		sortable: false,
	},
	{
		name: 'number',
		title: __( 'Account Number' ),
		sortable: true,
	},
	{
		name: 'bank_name',
		title: __( 'Bank Name' ),
		sortable: true,
	},
	{
		name: 'opening_balance',
		title: __( 'Opening Balance' ),
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
