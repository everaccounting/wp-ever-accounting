export default class Money {
	/**
	 * Currencies configs.
	 *
	 * @since 3.0
	 * @type {Object}
	 */
	currencies = {
		USD: {
			code: 'USD',
			rate: 1,
			precision: 2,
			symbol: '$',
			position: 'before',
			thousand: ',',
			decimal: '.',
		},
	};

	/**
	 * Current currency config.
	 *
	 * @type {Object}
	 * @since 3.0
	 */
	config = {
		code: 'USD',
		rate: 1,
		precision: 2,
		symbol: '$',
		position: 'before',
		thousand: ',',
		decimal: '.',
	};

	/**
	 * Creates a new Money instance.
	 *
	 * @since 3.0
	 * @param {string} currency The currency code.
	 */
	constructor( currency ) {
		// Set up the currencies if eac_currencies is available.
		if ( typeof window?.eac_currencies !== 'undefined' ) {
			// eslint-disable-next-line no-undef
			this.currencies = eac_currencies;
		}

		if ( this.currencies.hasOwnProperty( currency ) ) {
			this.config = {
				...this.config,
				...this.currencies[ currency ],
			};
		}
	}

	/**
	 * Format a number.
	 *
	 * @param {number} amount
	 * @since 3.0
	 * @return {string} The formatted number.
	 */
	format( amount ) {
		// Clean up the amount:
		amount = this.unformat( amount );

		// Handle negative amounts
		const negative = amount < 0;
		amount = Math.abs( amount );

		// Format the number with decimals
		const parts = amount.toFixed( this.config.precision ).split( '.' );
		const integerPart = parts[ 0 ].replace( /\B(?=(\d{3})+(?!\d))/g, this.config.thousand ); // Add thousand separators

		const decimalPart = parts.length > 1 ? this.config.decimal + parts[ 1 ] : '';

		// Construct the formatted value
		const formattedAmount =
			this.config.position === 'before'
				? this.config.symbol + integerPart + decimalPart
				: integerPart + decimalPart + this.config.symbol;

		return negative ? '-' + formattedAmount : formattedAmount;
	}

	/**
	 * Unformat a number.
	 *
	 * @param {number|string} amount
	 * @since 3.0
	 * @return {number} The unformatted number.
	 */
	unformat( amount ) {
		// Handle null, undefined, or empty string values
		if ( amount === undefined || amount === null || amount === '' ) {
			return 0;
		}

		// Return the amount as-is if it's already a number:
		if ( typeof amount === 'number' ) {
			return amount;
		}

		const decimal = this.config.decimal || '.';

		// Build regex to strip out everything except digits, decimal point and minus sign:
		const regex = new RegExp( `[^0-9-\\${ decimal }]`, 'g' );
		const unformatted = parseFloat(
			String( amount )
				.replace( /$(.*)$/, '-$1' ) // replace bracketed values with negatives
				.replace( regex, '' ) // strip out any cruft
				.replace( decimal, '.' ) // make sure decimal point is standard
		);

		return ! isNaN( unformatted ) ? unformatted : 0;
	}

	/**
	 * Get the absolute integer value of an amount.
	 *
	 * @param {number|string} amount The amount to get the absolute integer of.
	 * @return {number} The absolute integer value.
	 */
	absint( amount ) {
		return Math.abs( Math.round( this.unformat( amount ) ) );
	}
}
