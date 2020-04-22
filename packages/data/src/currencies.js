/**
 * Internal dependencies
 */
import data from './data';
/**
 * External dependencies
 */
import { forOwn } from 'lodash';
/**
 * Provided via the data passed along by the server.
 * This data a configuration object passed along from the server that indicates
 * the default currency settings from the server.
 *
 * @type {{}}
 */
export const {
	currency: CURRENCY = {
		name: 'US Dollar',
		code: 'USD',
		currency_code: 840,
		precision: 2,
		subunit: 100,
		symbol: '$',
		position: 'before',
		decimalSeparator: '.',
		thousandSeparator: ',',
	},
} = data;

/**
 * Exports all the currency configs from server
 *
 * @type {*[]}
 */
export const { global_currencies: GLOBAL_CURRENCIES = [] } = data;

/**
 * Returns the specific currency configs from server
 *
 * @param {string} code
 * @return {*|{symbol: string, thousandSeparator: string, code: string, decimalSeparator: string, precision: number, name: string, subunit: number, position: string, currency_code: number}}
 */
export const getCurrencyConfig = code => {
	return GLOBAL_CURRENCIES[code] || CURRENCY;
};

/**
 * return a select list
 *
 * @return {{label: string, value: string}[]}
 */
export const getGlobalCurrencies = () => {
	return Object.keys(GLOBAL_CURRENCIES).map(key => {
		const value = GLOBAL_CURRENCIES[key];
		return {
			label: `${value.name} (${value.code})`,
			value: key,
		};
	});
};
