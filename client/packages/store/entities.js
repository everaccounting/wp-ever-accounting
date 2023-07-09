import { __ } from '@wordpress/i18n';
import { pascalCase } from 'change-case';

export const defaultEntities = [
	{
		name: 'category',
		baseURL: '/eac/v1/categories',
		baseURLParams: { context: 'edit' },
		plural: 'categories',
		label: __('Category'),
		key: 'id',
	},
	{
		name: 'tax',
		baseURL: '/eac/v1/taxes',
		baseURLParams: { context: 'edit' },
		plural: 'taxes',
		label: __('Tax'),
		key: 'id',
	},
	{
		name: 'currency',
		baseURL: '/eac/v1/currencies',
		baseURLParams: { context: 'edit' },
		plural: 'currencies',
		label: __('Currency'),
		key: 'id',
	},
	{
		name: 'account',
		baseURL: '/eac/v1/accounts',
		baseURLParams: { context: 'edit' },
		plural: 'accounts',
		label: __('Account'),
	},
	{
		name: 'payment',
		baseURL: '/eac/v1/payments',
		baseURLParams: { context: 'edit' },
		plural: 'payments',
		label: __('Payment'),
	},
	{
		name: 'invoice',
		baseURL: '/eac/v1/invoices',
		baseURLParams: { context: 'edit' },
		plural: 'invoices',
		label: __('Invoice'),
	},
	{
		name: 'bill',
		baseURL: '/eac/v1/bills',
		baseURLParams: { context: 'edit' },
		plural: 'bills',
		label: __('Bill'),
	},
	{
		name: 'transfer',
		baseURL: '/eac/v1/transfers',
		baseURLParams: { context: 'edit' },
		plural: 'transfers',
		label: __('Transfer'),
	},
	{
		name: 'expense',
		baseURL: '/eac/v1/expenses',
		baseURLParams: { context: 'edit' },
		plural: 'expenses',
		label: __('Expense'),
	},
	{
		name: 'customer',
		baseURL: '/eac/v1/customers',
		baseURLParams: { context: 'edit' },
		plural: 'customers',
		label: __('Customer'),
	},
	{
		name: 'vendor',
		baseURL: '/eac/v1/vendors',
		baseURLParams: { context: 'edit' },
		plural: 'vendors',
		label: __('Vendor'),
	},
];

/**
 * Returns the entity's getter method name given its name.
 *
 * @example
 * ```js
 * const nameSingular = getMethodName( 'theme', 'get' );
 * // nameSingular is getRootTheme
 *
 * const namePlural = getMethodName('theme', 'set' );
 * // namePlural is setRootThemes
 * ```
 *
 * @param {string}  name      Entity name.
 * @param {string}  prefix    Function prefix.
 * @param {boolean} usePlural Whether to use the plural form or not.
 *
 * @return {string} Method name
 */
export const getMethodName = (name, prefix = 'get', usePlural = false) => {
	const entityConfig = defaultEntities.find((config) => config.name === name);
	const nameSuffix = pascalCase(name) + (usePlural ? 's' : '');
	const suffix =
		usePlural && 'plural' in entityConfig && entityConfig?.plural
			? pascalCase(entityConfig.plural)
			: nameSuffix;
	return `${prefix}${suffix}`;
};
