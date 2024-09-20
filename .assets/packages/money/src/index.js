export const Money = class Money {
	/**
	 * Creates a new Money instance.
	 *
	 * @since 3.0
	 *
	 * @param {object} currency The currency
	 */
	constructor(currency = {}) {
		const {
			code = 'USD',
			rate = 1,
			decimals = 2,
			subunit = 2,
			symbol = '$',
			position = 'before',
			thousand_separator = ',',
			decimal_separator = '.'
		} = currency;

		this.code = code;
		this.rate = rate;
		this.decimals = decimals;
		this.subunit = subunit;
		this.symbol = symbol;
		this.position = position;
		this.thousand_separator = thousand_separator;
		this.decimal_separator = decimal_separator;
	}

	/**
	 * Format a number.
	 *
	 * @since 3.0
	 * @return {string} The formatted number.
	 */
	format(amount) {
		// Clean up the amount:
		amount = this.unformat(amount);

		// Handle negative amounts
		const negative = amount < 0;
		amount = Math.abs(amount);

		// Format the number with decimals
		const parts = amount.toFixed(this.decimals).split('.');
		let integerPart = parts[0]
			.replace(/\B(?=(\d{3})+(?!\d))/g, this.thousand_separator); // Add thousand separators

		const decimalPart = parts.length > 1 ? this.decimal_separator + parts[1] : '';

		// Construct the formatted value
		const formattedAmount = this.position === 'before'
			? this.symbol + integerPart + decimalPart
			: integerPart + decimalPart + this.symbol;

		return negative ? '-' + formattedAmount : formattedAmount;
	}

	/**
	 * Unformat a number.
	 *
	 * @since 3.0
	 * @return {number} The unformatted number.
	 */
	unformat(amount) {
		// Fails silently (need decent errors):
		amount = amount || 0;

		// Return the amount as-is if it's already a number:
		if (typeof amount === "number") return amount;

		const decimal = this.decimal_separator || ".";

		// Build regex to strip out everything except digits, decimal point and minus sign:
		const regex = new RegExp("[^0-9-" + decimal + "]", ["g"]),
			unformatted = parseFloat(
				("" + amount)
					.replace(/$(.*)$/, "-$1") // replace bracketed values with negatives
					.replace(regex, '')         // strip out any cruft
					.replace(decimal, '.')      // make sure decimal point is standard
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
}
