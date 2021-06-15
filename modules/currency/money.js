import {sprintf} from '@wordpress/i18n';
import {numberFormat} from '@eaccounting/number';
import {DEFAULT_CURRENCY} from '@eaccounting/data';

export const Money = (currency = DEFAULT_CURRENCY) => {

	/**
	 * Strip tags.
	 *
	 * @param str
	 * @returns {string|string}
	 */
	function stripTags(str) {
		const tmp = document.createElement('DIV');
		tmp.innerHTML = str;
		return tmp.textContent || tmp.innerText || '';
	}


	/**
	 * Get the default price format from a currency.
	 *
	 * @param {string} position Currency configuration.
	 * @return {string} Price format.
	 */
	function getPriceFormat(position) {
		position = stripTags(position);

		switch (position) {
			case 'before':
				return '%1$s%2$s';
			case 'after':
				return '%2$s%1$s';
		}

		return '%1$s%2$s';
	}

	/**
	 * Format amount.
	 *
	 * @returns {string|?string}
	 */
	function formatAmount(amount) {
		const formattedNumber = numberFormat(currency, amount);

		if (formattedNumber === '') {
			return formattedNumber;
		}
		const {symbol, position} = currency;
		const priceFormat = getPriceFormat(position);

		return sprintf(priceFormat, symbol, formattedNumber);
	}

	/**
	 * Get the rounded decimal value of a number at the precision used for the current currency.
	 * @param {number|string} number A floating point number (or integer), or string that converts to a number
	 * @return {number} The original number rounded to a decimal point
	 */
	function formatDecimal(number) {
		if (typeof number !== 'number') {
			number = parseFloat(number);
		}
		if (Number.isNaN(number)) {
			return 0;
		}
		const {precision} = currency;
		return (
			Math.round(number * Math.pow(10, precision)) /
			Math.pow(10, precision)
		);
	}

	/**
	 * Get the string representation of a floating point number to the precision used by the current currency.
	 * This is different from `formatAmount` by not returning the currency symbol.
	 *
	 * @param  {number|string} number A floating point number (or integer), or string that converts to a number
	 * @return {string}               The original number rounded to a decimal point
	 */
	function formatDecimalString(number) {
		if (typeof number !== 'number') {
			number = parseFloat(number);
		}
		if (Number.isNaN(number)) {
			return '';
		}
		const {precision} = currency;
		return number.toFixed(precision);
	}

	/**
	 * Render a currency for display in a component.
	 *
	 * @param  {number|string} number A floating point number (or integer), or string that converts to a number
	 * @return {Node|string} The number formatted as currency and rendered for display.
	 */
	function render(number) {
		if (typeof number !== 'number') {
			number = parseFloat(number);
		}
		if (number < 0) {
			return (
				<span className="is-negative">
						{formatAmount(number)}
					</span>
			);
		}
		return formatAmount(number);
	}


	return {
		formatAmount,
		formatDecimal,
		formatDecimalString,
		render
	}


}

