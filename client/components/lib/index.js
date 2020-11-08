import { dispatch } from '@wordpress/data';

/**
 * Get a portal node, or create it if it doesn't exist.
 *
 * @param {string} portalName DOM ID of the portal
 * @param portalWrapper
 * @returns {Element}
 */
export function getPortal(portalName, portalWrapper = 'wpbody') {
	let portal = document.getElementById(portalName);

	if (portal === null) {
		const wrapper = document.getElementById(portalWrapper);

		portal = document.createElement('div');

		if (wrapper && wrapper.parentNode) {
			portal.setAttribute('id', portalName);
			wrapper.parentNode.appendChild(portal);
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
 * @param number
 * @param decimals
 * @param dec_point
 * @param thousands_sep
 */
export const numberFormat = (number, decimals, dec_point, thousands_sep) => {
	number = (number + '').replace(/[^0-9+\-Ee.]/g, '');
	let n = !isFinite(+number) ? 0 : +number,
		prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
		sep = typeof thousands_sep === 'undefined' ? ',' : thousands_sep,
		dec = typeof dec_point === 'undefined' ? '.' : dec_point,
		s = '',
		toFixedFix = function (n, prec) {
			const k = Math.pow(10, prec);
			return '' + (Math.round(n * k) / k).toFixed(prec);
		};
	// Fix for IE parseFloat(0.55).toFixed(0) = 0;
	s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
	if (s[0].length > 3) {
		s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
	}
	if ((s[1] || '').length < prec) {
		s[1] = s[1] || '';
		s[1] += new Array(prec - s[1].length + 1).join('0');
	}
	return s.join(dec);
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

