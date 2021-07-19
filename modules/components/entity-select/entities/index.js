/**
 * Internal dependencies
 */
import AccountModal from '../../forms/account';
import CurrencyModal from '../../forms/currency';
import CustomerModal from '../../forms/customer';
import VendorModal from '../../forms/vendor';
import ItemModal from '../../forms/item';
import CategoryModal from '../../forms/category';

export const accounts = {
	entityName: 'accounts',
	getOptionLabel: ( account ) =>
		`${ account.name } (${ account.currency.code })`,
	getOptionValue: ( account ) => account && account.id,
	modal: <AccountModal />,
};

export const currencies = {
	entityName: 'currencies',
	getOptionLabel: ( currency ) => `${ currency.name } (${ currency.symbol })`,
	getOptionValue: ( currency ) => currency && currency.code,
	modal: <CurrencyModal />,
};

export const customers = {
	entityName: 'customers',
	getOptionLabel: ( customer ) => customer && customer.name,
	getOptionValue: ( customer ) => customer && customer.id,
	modal: <CustomerModal />,
};

export const vendors = {
	entityName: 'vendors',
	getOptionLabel: ( customer ) => customer && customer.name,
	getOptionValue: ( customer ) => customer && customer.id,
	modal: <VendorModal />,
};

export const items = {
	entityName: 'items',
	getOptionLabel: ( item ) => `${ item.name }`,
	getOptionValue: ( item ) => item && item.id,
	modal: <ItemModal />,
};

export const expenseCategories = {
	entityName: 'categories',
	baseQuery: { type: 'expense' },
	getOptionLabel: ( category ) => category && category.name,
	getOptionValue: ( category ) => category && category.id,
	modal: <CategoryModal type={ 'expense' } />,
};

export const incomeCategories = {
	entityName: 'categories',
	baseQuery: { type: 'income' },
	getOptionLabel: ( category ) => category && category.name,
	getOptionValue: ( category ) => category && category.id,
	modal: <CategoryModal type={ 'income' } />,
};

export const itemCategories = {
	entityName: 'categories',
	baseQuery: { type: 'item' },
	getOptionLabel: ( category ) => category && category.name,
	getOptionValue: ( category ) => category && category.id,
	modal: <CategoryModal type={ 'item' } />,
};
