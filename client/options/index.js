import {__} from '@wordpress/i18n';

const options = {
	categoryTypes: [
		{
			label: __('Income'),
			value: 'income'
		},
		{
			label: __('Expense'),
			value: 'expense'
		},
		{
			label: __('Items'),
			value: 'item'
		}
	]
};

export const getOptions = (name, fallback = false, filter = val => val) => {
	const value = options.hasOwnProperty(name) ? options[name] : fallback;
	return filter(value, fallback);
};
