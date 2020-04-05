import data from './data';
import {forOwn} from "lodash"
/**
 * Provided via the data passed along by the server.
 * This data a configuration object passed along from the server that indicates
 * the default currency settings from the server.
 * @type {{}}
 */
export const {
	currency: CURRENCY = {
		name: "US Dollar",
		code: "USD",
		currency_code: 840,
		precision: 2,
		subunit: 100,
		symbol: "$",
		position: "before",
		decimalSeparator: ".",
		thousandSeparator: ","
	}
} = data;

/**
 * Exports all the currency configs from server
 * @type {*[]}
 */
export const {currency_configs: CURRENCY_CONFIGS = []} = data;


/**
 * Returns the specific currency configs from server
 *
 * @param {String} code
 * @returns {*|{symbol: string, thousandSeparator: string, code: string, decimalSeparator: string, precision: number, name: string, subunit: number, position: string, currency_code: number}}
 */
export const getCurrencyConfig = (code) => {
	return CURRENCY_CONFIGS[code] || CURRENCY;
};

/**
 * return a select list
 * @returns {{label: string, value: string}[]}
 */
export const getGlobalCurrencies = () => {
	return Object.keys(CURRENCY_CONFIGS).map((key) => {
		const value = CURRENCY_CONFIGS[key];
		return {
			label: `${value.name} (${value.code})`,
			value: key
		}
	});
};
