/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';

export const defaultEntitiesConfig = [
	{
		name: 'item',
		baseURL: '/eac/v1/items',
		baseURLParams: { context: 'edit' },
		plural: 'items',
		label: __( 'Item', 'wp-ever-accounting' ),
		key: 'id',
	},
	{
		name: 'payment',
		baseURL: '/eac/v1/payments',
		baseURLParams: { context: 'edit' },
		plural: 'payments',
		label: __( 'Payment', 'wp-ever-accounting' ),
	},
	{
		name: 'invoice',
		baseURL: '/eac/v1/invoices',
		baseURLParams: { context: 'edit' },
		plural: 'invoices',
		label: __( 'Invoice', 'wp-ever-accounting' ),
		getStatuses: () => [
			{ label: __( 'Draft', 'wp-ever-accounting' ), value: 'draft' },
			{ label: __( 'Pending', 'wp-ever-accounting' ), value: 'pending' },
			{ label: __( 'Paid', 'wp-ever-accounting' ), value: 'paid' },
		],
	},
	{
		name: 'customer',
		baseURL: '/eac/v1/customers',
		baseURLParams: { context: 'edit' },
		plural: 'customers',
		label: __( 'Customer', 'wp-ever-accounting' ),
	},
	{
		name: 'expense',
		baseURL: '/eac/v1/expenses',
		baseURLParams: { context: 'edit' },
		plural: 'expenses',
		label: __( 'Expense', 'wp-ever-accounting' ),
	},
	{
		name: 'bill',
		baseURL: '/eac/v1/bills',
		baseURLParams: { context: 'edit' },
		plural: 'bills',
		label: __( 'Bill', 'wp-ever-accounting' ),
	},
	{
		name: 'vendor',
		baseURL: '/eac/v1/vendors',
		baseURLParams: { context: 'edit' },
		plural: 'vendors',
		label: __( 'Vendor', 'wp-ever-accounting' ),
	},
	{
		name: 'account',
		baseURL: '/eac/v1/accounts',
		baseURLParams: { context: 'edit' },
		plural: 'accounts',
		label: __( 'Account', 'wp-ever-accounting' ),
	},
	{
		name: 'transfer',
		baseURL: '/eac/v1/transfers',
		baseURLParams: { context: 'edit' },
		plural: 'transfers',
		label: __( 'Transfer', 'wp-ever-accounting' ),
	},
	{
		name: 'currency',
		baseURL: '/eac/v1/currencies',
		baseURLParams: { context: 'edit' },
		plural: 'currencies',
		label: __( 'Currency', 'wp-ever-accounting' ),
		key: 'id',
	},
	{
		name: 'category',
		baseURL: '/eac/v1/categories',
		baseURLParams: { context: 'edit' },
		plural: 'categories',
		label: __( 'Category', 'wp-ever-accounting' ),
		key: 'id',
	},
	{
		name: 'tax',
		baseURL: '/eac/v1/taxes',
		baseURLParams: { context: 'edit' },
		plural: 'taxes',
		label: __( 'Tax', 'wp-ever-accounting' ),
		key: 'id',
	},
];
