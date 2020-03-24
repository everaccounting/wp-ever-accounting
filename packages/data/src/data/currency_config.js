import data from './data';

/**
 * Provided via the data passed along by the server.
 * This data a configuration object passed along from the server that indicates
 * the default currency settings from the server.
 * @type {{}}
 */
export const {
	currency_config: currencyConfig = {
		name: "US Dollar",
		currency: "USD",
		code: 840,
		precision: 2,
		subunit: 100,
		symbol: "$",
		position: "before",
		decimalSeparator: ".",
		thousandSeparator: ","
	}
} = data;

