/**
 * External dependencies
 */
import moment from 'moment';
import PropTypes from 'prop-types';
/**
 * WordPress dependencies
 */
import { dispatch } from '@wordpress/data';

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
export const numberFormat = (number, decimals, decPoint, thousandsSep) => {
	number = (number + '').replace(/[^0-9+\-Ee.]/g, '');
	return number;
};

export function createNoticesFromResponse(response) {
	const { createErrorNotice } = dispatch('core/notices');
	if (response && response.error_data && response.errors && Object.keys(response.errors).length) {
		// Loop over multi-error responses.
		Object.keys(response.errors).forEach((errorKey) => {
			createErrorNotice(response.errors[errorKey].join(' '));
		});
	} else if (response && response.message) {
		createErrorNotice(response.message);
	}
}

/**
 * Convert a string to Moment object
 *
 * @param {string} str - date string
 * @return {Object|null} - Moment object representing given string
 */
export function toMoment(str) {
	if (typeof str === 'string') {
		const date = moment(str);
		return date.isValid() ? date : null;
	}
	if (moment.isMoment(str)) {
		return str.isValid() ? str : null;
	}
	throw new Error('toMoment requires a string to be passed as an argument');
}
