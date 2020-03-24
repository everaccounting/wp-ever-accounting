/* global document, eAccounting */
import {__} from "@wordpress/i18n";

/**
 * This will hold arbitrary data assigned by the Assets Registry.
 * @type {{}}
 */

const defaults = {
	currency_config: {
		code: 'USD',
		precision: 2,
		subunit: 100,
		symbol: '$',
		position: "before",
		decimalSeparator: '.',
		thousandSeparator: ',',
	},
	site_formats: {
		date_formats: {
			moment: "MMMM D, YYYY h:mm a",
			moment_split: {
				date: "MMMM D, YYYY",
				time: "h:mm a"
			}
		}
	},
	locale: {
		user: 'en',
		site: 'en',
	},
	transactionTypes: [
		{
			label: __('Income'),
			value: 'income'
		},
		{
			label: __('Expense'),
			value: 'expense'
		},
		{
			label: __('Transfer'),
			value: 'transfer'
		}
	]
};

const site = typeof eAccounting === 'object' && typeof eAccounting.data ? eAccounting.data : {};
const data = {
	...defaults,
	...site,
};

export default data;
