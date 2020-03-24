/**
 * External imports
 */
import {
	isEmpty,
	isString,
	isNumber,
	isUndefined,
} from 'lodash';
import { Exception } from './exceptions';
import { currencyConfig as CURRENCY_CONFIG } from './currency_config';
import warning from 'warning';

/**
 * A value object representing currency values
 */
export class Currency {
	/**
	 * The ISO 4217 code identifying the currency (eg. 'USD')
	 * @type {string}
	 */
	code = '';


	/**
	 * The currency symbol (eg. '$');
	 * @type {string}
	 */
	symbol = '';

	/**
	 * Whether the currency symbol is displayed before or after the value.
	 * @type {boolean}
	 */
	signB4 = true;

	/**
	 * The precision for the value (eg. 10.02 is 2, 10.123 is 3). The number of
	 * decimal places can be used to calculate the number of subunit for the
	 * currency - subunit = pow( 10, precision).
	 * @type {number}
	 */
	precision = 2;

	/**
	 * The symbol used for the decimal mark (eg. '.')
	 * @type {string}
	 */
	decimalSeparator = '.';

	/**
	 * The symbol used to split up thousands in the value (eg. ',')
	 * @type {string}
	 */
	thousandSeparator = ',';

	/**
	 * The number of fractional divisions of a currency's main unit.  If not
	 * provided, then it is automatically calculated from the precision
	 * value.
	 * @type {number}
	 */
	subunit = 100;

	/**
	 * Constructor
	 * @param {{}} currencyConfig An object containing the configuration for
	 * this currency value object.  On construction, the Currency object is
	 * frozen so that it becomes immutable.
	 */
	constructor( currencyConfig ) {
		Currency.validateCurrencyConfig( currencyConfig );
		this.code = currencyConfig.currency;
		this.symbol = currencyConfig.symbol;
		this.signB4 = isUndefined( currencyConfig.position ) || currencyConfig.position === "before" ? this.signB4 : false ;
		this.precision = isUndefined( currencyConfig.precision ) ? this.precision : currencyConfig.precision;
		this.decimalSeparator = currencyConfig.decimalSeparator || this.decimalSeparator;
		this.thousandSeparator = currencyConfig.thousandSeparator || this.thousandSeparator;
		this.subunit = currencyConfig.subunit || Math.pow( 10, this.precision ); Object.freeze( this );
	}

	/**
	 * Returns the currency properties as an object formatted for the
	 * accounting-js library configuration.
	 * @return {{}}  An object shaped for what the accounting-js library expects
	 */
	toAccountingSettings() {
		const decimalInfo = {
			decimal: this.decimalSeparator,
			thousand: this.thousandSeparator,
			precision: this.precision,
		};
		return {
			currency: {
				symbol: this.symbol,
				format: {
					pos: this.signB4 ? '%s%v' : '%v%s',
					neg: this.signB4 ? '- $s%v' : '- %v%s',
					zero: this.signB4 ? '%s%v' : '%v%s',
				},
				...decimalInfo,
			},
			number: decimalInfo,
		};
	}

	/**
	 * Returns JSON representation of this object.
	 * @return {Object} Function returning the object to be serialized by
	 * JSON.stringify
	 */
	toJSON() {
		return {
			code: this.code,
			symbol: this.symbol,
			signB4: this.signB4,
			decimalSeparator: this.decimalSeparator,
			thousandSeparator: this.thousandSeparator,
			subunit: this.subunit,
			precision: this.precision,
		};
	}

	/**
	 * This validates whether the passed in config has the required properties
	 * (and correct types) for constructing a Currency object.
	 *
	 * @param {{}} config
	 * @throws {Exception}
	 * @throws {TypeError}
	 */
	static validateCurrencyConfig = ( config ) => {
		if ( isEmpty( config ) ) {
			throw new Exception(
				'The configuration object provided to Currency must not' +
				' be empty'
			);
		}
		if ( ! config.currency || ! isString( config.currency ) ) {
			throw new TypeError(
				'The configuration object provided to Currency must have ' +
				'a "currency" property that is a string.'
			);
		}

		if ( ! config.symbol || ! isString( config.symbol ) ) {
			throw new TypeError(
				'The configuration object provided to Currency must have a ' +
				'"sign" property that is a string.'
			);
		}

		if ( config.position && ! isString( config.position ) ) {
			throw new TypeError(
				'The position property on the configuration object ' +
				'must be a string primitive.'
			);
		}

		if ( config.precision && ! isNumber( config.precision ) ) {
			throw new TypeError(
				'The precision property on the configuration object ' +
				'must be a number primitive'
			);
		}

		if ( config.decimalSeparator && ! isString( config.decimalSeparator ) ) {
			throw new TypeError(
				'The decimalSeparator property on the configuration object ' +
				'must be a string primitive.'
			);
		}

		if ( config.thousandSeparator &&
			! isString( config.thousandSeparator ) ) {
			throw new TypeError(
				'The thousandSeparator property on the configuration object ' +
				'must be a string primitive.'
			);
		}

		if ( config.subunit && ! isNumber( config.subunit ) ) {
			throw new TypeError(
				'The subunit property on the configuration object ' +
				'must be a number primitive.'
			);
		}
	}
}

/**
 * Export of a Currency Value object created from a currency config provided.
 * This catches any exception and triggers a console error.
 *
 * @param {{}} config
 * @return {Currency|{}} If there's a problem constructing the currency object
 * an empty object is returned.
 */
export const SiteCurrency = ( config = {} ) => {
	let currency;
	try {
		currency = new Currency( config );
	} catch ( e ) {
		currency = {};
		warning(
			false,
			'The Site Currency object could not be created because ' +
			'of this error: ' + e.message
		);
	}
	return currency;
};

export default SiteCurrency( CURRENCY_CONFIG );
