/**
 * Internal dependencies
 */
import { DEFAULT_CURRENCY } from './site-data';
import { numberFormat } from './number-utils';
/**
 * WordPress dependencies
 */
import { sprintf } from '@wordpress/i18n';

/**
 * Get the default price format from a currency.
 *
 * @param {string} position Currency configuration.
 * @return {string} Price format.
 */
export function getPriceFormat( position ) {
	switch ( position ) {
		case 'before':
			return '%1$s%2$s';
		case 'after':
			return '%2$s%1$s';
	}

	return '%1$s%2$s';
}

/**
 * Parse amount from formatted value to raw
 *
 * @param {string} value Amount
 * @param {string}  decimal decimal
 * @param  {string|number} fallback Fallback amount.
 * @return {number|number|number|*}
 */
export function parseAmount( value, decimal = '.', fallback = 0 ) {
	// Recursively unformat arrays:
	if ( Array.isArray( value ) ) {
		return value.map( function ( val ) {
			return parseAmount( val, decimal, fallback );
		} );
	}

	// Return the value as-is if it's already a number:
	if ( typeof value === 'number' ) return value;

	// Build regex to strip out everything except digits, decimal point and minus sign:
	const regex = new RegExp( '[^0-9-(-)-' + decimal + ']', [ 'g' ] );
	const unformattedValueString = ( '' + value )
		.replace( regex, '' ) // strip out any cruft
		.replace( decimal, '.' ) // make sure decimal point is standard
		.replace( /\(([-]*\d*[^)]?\d+)\)/g, '-$1' ) // replace bracketed values with negatives
		.replace( /\((.*)\)/, '' ); // remove any brackets that do not have numeric value

	/**
	 * Handling -ve number and bracket, eg.
	 * (-100) = 100, -(100) = 100, --100 = 100
	 */
	const negative = ( unformattedValueString.match( /-/g ) || 2 ).length % 2,
		absUnformatted = parseFloat(
			unformattedValueString.replace( /-/g, '' )
		),
		unformatted = absUnformatted * ( negative ? -1 : 1 );

	// This will fail silently which may cause trouble, let's wait and see:
	return ! isNaN( unformatted ) ? unformatted : fallback;
}

/**
 * Format amount to raw.
 *
 * @param {string} amount
 * @param {Object} currency
 * @return {string|number}
 * @class
 */
export function formatAmount( amount, currency = DEFAULT_CURRENCY ) {
	const { symbol, position, decimal_separator } = currency;
	const formattedNumber = numberFormat(
		currency,
		parseAmount( amount, decimal_separator )
	);
	if ( ! formattedNumber || formattedNumber === '' ) {
		return 0;
	}
	const format = getPriceFormat( position );
	return sprintf( format, symbol, formattedNumber );
}

/**
 * Format decimal.
 *
 * @param {string} amount
 * @param {Object} currency
 * @return {*|number|number|number}
 */
export function formatDecimal( amount, currency = DEFAULT_CURRENCY ) {
	const { decimal_separator } = currency;
	return parseAmount( amount, decimal_separator );
}
