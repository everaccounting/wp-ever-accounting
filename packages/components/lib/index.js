/**
 * WordPress dependencies
 */
import { dispatch } from '@wordpress/data';

/**
 * Get a portal node, or create it if it doesn't exist.
 *
 * @param {string} portalName    DOM ID of the portal
 * @param {string} portalWrapper
 * @return {Function} Element
 */
export function getPortal( portalName, portalWrapper = 'wpbody' ) {
	let portal = document.getElementById( portalName );

	if ( portal === null ) {
		const wrapper = document.getElementById( portalWrapper );

		portal = document.createElement( 'div' );

		if ( wrapper && wrapper.parentNode ) {
			portal.setAttribute( 'id', portalName );
			wrapper.parentNode.appendChild( portal );
		}
	}

	return portal;
}

/**
 * Exposes number format capability through i18n mixin
 *
 * @copyright Copyright (c) 2013 Kevin van Zonneveld (http://kvz.io) and Contributors (http://phpjs.org/authors).
 * @license See CREDITS.md
 * @see https://github.com/kvz/phpjs/blob/ffe1356af23a6f2512c84c954dd4e828e92579fa/functions/strings/number_format.js
 * @param {number} number
 * @param {number} decimals
 * @param {number} decPoint
 * @param {string} thousandsSep
 */
export const numberFormat = ( number, decimals, decPoint, thousandsSep ) => {
	number = ( number + '' ).replace( /[^0-9+\-Ee.]/g, '' );
	// eslint-disable-next-line prefer-const
	let n = ! isFinite( +number ) ? 0 : +number,
		// eslint-disable-next-line prefer-const
		prec = ! isFinite( +decimals ) ? 0 : Math.abs( decimals ),
		// eslint-disable-next-line prefer-const
		sep = typeof thousandsSep === 'undefined' ? ',' : thousandsSep,
		// eslint-disable-next-line prefer-const
		dec = typeof decPoint === 'undefined' ? '.' : decPoint,
		s = '',
		// eslint-disable-next-line prefer-const
		toFixedFix = function ( n, prec ) {
			const k = Math.pow( 10, prec );
			return '' + ( Math.round( n * k ) / k ).toFixed( prec );
		};
	// Fix for IE parseFloat(0.55).toFixed(0) = 0;
	s = ( prec ? toFixedFix( n, prec ) : '' + Math.round( n ) ).split( '.' );
	if ( s[ 0 ].length > 3 ) {
		s[ 0 ] = s[ 0 ].replace( /\B(?=(?:\d{3})+(?!\d))/g, sep );
	}
	if ( ( s[ 1 ] || '' ).length < prec ) {
		s[ 1 ] = s[ 1 ] || '';
		s[ 1 ] += new Array( prec - s[ 1 ].length + 1 ).join( '0' );
	}
	return s.join( dec );
};

export function createNoticesFromResponse( response ) {
	const { createNotice } = dispatch( 'core/notices' );

	if (
		response.error_data &&
		response.errors &&
		Object.keys( response.errors ).length
	) {
		// Loop over multi-error responses.
		Object.keys( response.errors ).forEach( ( errorKey ) => {
			createNotice( 'error', response.errors[ errorKey ].join( ' ' ) );
		} );
	} else if ( response.message ) {
		// Handle generic messages.
		createNotice( response.code ? 'error' : 'success', response.message );
	}
}
