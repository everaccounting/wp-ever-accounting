/**
 * External dependencies
 */
import { sprintf } from '@wordpress/i18n';
import { numberFormat } from './numbers';
import { getCurrencyData } from './currencies';

export default class Currency {
	constructor(currency = null) {
		if (!this.code) {
			this.setCurrency(currency);
		}
	}

	/**
	 * Set the currency configuration to use for the class.
	 *
	 * @param {Object} currency An object containing currency configuration settings.
	 */
	setCurrency(currency = 'USD') {
		const config = getCurrencyData(currency);
		this.code = config.code.toString();
		this.symbol = config.symbol.toString();
		this.position = 'before';
		//this.position = config.position.toString() || "before";//todo uncomment this
		this.decimalSeparator = config.decimalSeparator.toString();
		this.priceFormat = this.getPriceFormat(config);
		this.thousandSeparator = config.thousandSeparator.toString();

		this.precision = parseInt(config.precision, 10);
	}

	stripTags(str) {
		const tmp = document.createElement('DIV');
		tmp.innerHTML = str;
		return tmp.textContent || tmp.innerText || '';
	}

	/**
	 * Get the default price format from a currency.
	 *
	 * @param {Object} config Currency configuration.
	 * @return {string} Price format.
	 */
	getPriceFormat(config) {
		if (config.priceFormat) {
			return this.stripTags(config.priceFormat.toString());
		}

		switch (config.position) {
			case 'left':
				return '%1$s%2$s';
			case 'right':
				return '%2$s%1$s';
			case 'left_space':
				return '%1$s&nbsp;%2$s';
			case 'right_space':
				return '%2$s&nbsp;%1$s';
		}

		return '%1$s%2$s';
	}

	/**
	 * Formats money value.
	 *
	 * @param   {number|string} number number to format
	 * @return {?string} A formatted string.
	 */
	formatCurrency(number) {
		const formattedNumber = numberFormat(this, number);

		if (formattedNumber === '') {
			return formattedNumber;
		}

		// eslint-disable-next-line @wordpress/valid-sprintf
		return sprintf(this.priceFormat, this.symbol, formattedNumber);
	}

	/**
	 * Get the rounded decimal value of a number at the precision used for the current currency.
	 *
	 * @param {number|string} number A floating point number (or integer), or string that converts to a number
	 * @return {number} The original number rounded to a decimal point
	 */
	formatDecimal(number) {
		if (typeof number !== 'number') {
			number = parseFloat(number);
		}
		if (Number.isNaN(number)) {
			return 0;
		}
		return Math.round(number * Math.pow(10, this.precision)) / Math.pow(10, this.precision);
	}

	/**
	 * Get the string representation of a floating point number to the precision used by the current currency.
	 * This is different from `formatCurrency` by not returning the currency symbol.
	 *
	 * @param  {number|string} number A floating point number (or integer), or string that converts to a number
	 * @return {string}               The original number rounded to a decimal point
	 */
	formatDecimalString(number) {
		if (typeof number !== 'number') {
			number = parseFloat(number);
		}
		if (Number.isNaN(number)) {
			return '';
		}
		return number.toFixed(this.precision);
	}

	/**
	 * Render a currency for display in a component.
	 *
	 * @param  {number|string} number A floating point number (or integer), or string that converts to a number
	 * @return {Node|string} The number formatted as currency and rendered for display.
	 */
	render(number) {
		if (typeof number !== 'number') {
			number = parseFloat(number);
		}
		if (number < 0) {
			return <span className="is-negative">{this.formatCurrency(number)}</span>;
		}
		return this.formatCurrency(number);
	}
}
