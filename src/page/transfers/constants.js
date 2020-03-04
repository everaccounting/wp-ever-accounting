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
		name: 'date',
		title: __( 'Date' ),
		primary: true,
		sortable: true,
	},
	{
		name: 'from_account',
		title: __( 'From Account' ),
		sortable: true,
	},
	{
		name: 'number',
		title: __( 'Account Number' ),
		sortable: true,
	},
	{
		name: 'to_account',
		title: __( 'To account' ),
		sortable: true,
	},
	{
		name: 'amount',
		title: __( 'Amount' ),
		sortable: true,
	},
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
