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
				value: 'active',
			},
			{
				label: __( 'Disabled' ),
				value: 'inactive',
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
		name: 'created_date',
		title: __( 'Created Date' ),
		primary: true,
		sortable: true,
	},
	{
		name: 'account',
		title: __( 'Account' ),
		sortable: true,
	},
	{
		name: 'period',
		title: __( 'Period' ),
		sortable: false,
	},
	{
		name: 'closing_balance',
		title: __( 'Closing Balance' ),
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
