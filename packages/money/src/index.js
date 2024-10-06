export const Money = class Money {

	/**
	 * Currencies.
	 *
	 * @type {Array} currencies
	 * @since 3.0
	 */
	static currencies = [];

	/**
	 * Creates a new Money instance.
	 *
	 * @since 3.0
	 * @param {Array} currencies The currencies object.
	 */
	constructor(currencies = []) {
		this.currencies = currencies;
	}

	/**
	 * Format a number.
	 *
	 * @param {number} amount The amount to format.
	 * @param {String} currency The currency code.
	 * @since 3.0
	 * @return {string} The formatted number.
	 */
	format(amount, currency = 'USD') {
		// Currency setup.
		const symbol = this.currencies[currency]?.symbol || '$';
		const position = this.currencies[currency]?.position || 'before';
		const precision = this.currencies[currency]?.precision || 2;
		const thousand_separator = this.currencies[currency]?.thousand_separator || ',';
		const decimal_separator = this.currencies[currency]?.decimal_separator || '.';

		// Clean up the amount:
		amount = this.unformat(amount, currency);

		// Handle negative amounts
		const negative = amount < 0;
		amount = Math.abs(amount);

		// Format the number with decimals
		const parts = amount.toFixed(precision).split('.');
		const integerPart = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, thousand_separator); // Add thousand separators

		const decimalPart = parts.length > 1 ? decimal_separator + parts[1] : '';

		// Construct the formatted value
		const formattedAmount =
			position === 'before'
				? symbol + integerPart + decimalPart
				: integerPart + decimalPart + this.symbol;

		return negative ? '-' + formattedAmount : formattedAmount;
	}

	/**
	 * Unformat a number.
	 *
	 * @param {number} amount The amount to unformat.
	 * @param {String} currency The currency of the amount.
	 * @since 3.0
	 * @return {number} The unformatted number.
	 */
	unformat(amount, currency = 'USD') {
		// Fails silently (need decent errors):
		amount = amount || 0;

		// Return the amount as-is if it's already a number:
		if (typeof amount === 'number') {
			return amount;
		}

		const decimal = this.currencies[currency]?.decimal_separator || '.';

		// Build regex to strip out everything except digits, decimal point and minus sign:
		const regex = new RegExp('[^0-9-' + decimal + ']', ['g']),
			unformatted = parseFloat(
				('' + amount)
					.replace(/$(.*)$/, '-$1') // replace bracketed values with negatives
					.replace(regex, '') // strip out any cruft
					.replace(decimal, '.') // make sure decimal point is standard
			);

		// This will fail silently which may cause trouble, let's wait and see:
		return !isNaN(unformatted) ? unformatted : 0;
	}

	/**
	 * Get the absolute integer value of an amount.
	 *
	 * @param {number} amount The amount to get the absolute integer of.
	 * @return {number} The absolute integer value.
	 */
	absint(amount) {
		return Math.abs(Math.round(this.unformat(amount)));
	}
};
