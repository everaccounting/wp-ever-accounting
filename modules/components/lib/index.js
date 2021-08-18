/**
 * External dependencies
 */
import moment from 'moment';
import PropTypes from 'prop-types';
/**
 * WordPress dependencies
 */
import { dispatch } from '@wordpress/data';
import { isEmpty } from 'fast-glob/out/utils/string';

/**
 * Get a portal node, or create it if it doesn't exist.
 *
 * @param {string} portalName DOM ID of the portal
 * @param {string} portalWrapper
 * @return {Function} Element
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
 * @param {number} number
 * @param {number} decimals
 * @param {number} decPoint
 * @param {string} thousandsSep
 */
export const numberFormat = (number, decimals, decPoint, thousandsSep) => {
	number = (number + '').replace(/[^0-9+\-Ee.]/g, '');
	// eslint-disable-next-line prefer-const
	let n = !isFinite(+number) ? 0 : +number,
		// eslint-disable-next-line prefer-const
		prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
		// eslint-disable-next-line prefer-const
		sep = typeof thousandsSep === 'undefined' ? ',' : thousandsSep,
		// eslint-disable-next-line prefer-const
		dec = typeof decPoint === 'undefined' ? '.' : decPoint,
		s = '',
		// eslint-disable-next-line prefer-const
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

export function createNoticesFromResponse(response) {
	const { createErrorNotice } = dispatch('core/notices');
	if (
		response &&
		response.error_data &&
		response.errors &&
		Object.keys(response.errors).length
	) {
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
	if (isEmpty(str)) {
		return str;
	}
	if (typeof str === 'string') {
		const date = moment(str);
		return date.isValid() ? date : null;
	}
	if (moment.isMoment(str)) {
		return str.isValid() ? str : null;
	}
	throw new Error('toMoment requires a string to be passed as an argument');
}

export const REACT_SELECT_PROPS = {
	'aria-describedby': PropTypes.string,
	'aria-label': PropTypes.string,
	'aria-labelledby': PropTypes.string,
	autoFocus: PropTypes.bool,
	backspaceRemovesValue: PropTypes.bool,
	blurInputOnSelect: PropTypes.bool,
	captureMenuScroll: PropTypes.bool,
	className: PropTypes.string,
	classNamePrefix: PropTypes.string,
	closeMenuOnSelect: PropTypes.bool,
	components: PropTypes.object,
	controlShouldRenderValue: PropTypes.bool,
	delimiter: PropTypes.string,
	escapeClearsValue: PropTypes.bool,
	filterOption: PropTypes.func,
	formatGroupLabel: PropTypes.func,
	formatOptionLabel: PropTypes.func,
	getOptionLabel: PropTypes.func,
	getOptionValue: PropTypes.func,
	hideSelectedOptions: PropTypes.bool,
	id: PropTypes.string,
	inputValue: PropTypes.string,
	inputId: PropTypes.string,
	instanceId: PropTypes.oneOfType([PropTypes.number, PropTypes.string]),
	isClearable: PropTypes.bool,
	isDisabled: PropTypes.bool,
	isLoading: PropTypes.bool,
	isOptionDisabled: PropTypes.func,
	isOptionSelected: PropTypes.func,
	isMulti: PropTypes.bool,
	isSearchable: PropTypes.bool,
	loadingMessage: PropTypes.func,
	minMenuHeight: PropTypes.number,
	maxMenuHeight: PropTypes.number,
	menuIsOpen: PropTypes.bool,
	menuPlacement: PropTypes.oneOf(['auto', 'bottom', 'top']),
	menuPosition: PropTypes.oneOf(['absolute', 'fixed']),
	menuPortalTarget: PropTypes.element,
	menuShouldBlockScroll: PropTypes.bool,
	menuShouldScrollIntoView: PropTypes.bool,
	name: PropTypes.string,
	noOptionsMessage: PropTypes.func,
	onBlur: PropTypes.func,
	onChange: PropTypes.func,
	onFocus: PropTypes.func,
	onInputChange: PropTypes.func,
	onKeyDown: PropTypes.func,
	onMenuOpen: PropTypes.func,
	onMenuClose: PropTypes.func,
	onMenuScrollToTop: PropTypes.func,
	onMenuScrollToBottom: PropTypes.func,
	openMenuOnFocus: PropTypes.bool,
	openMenuOnClick: PropTypes.bool,
	options: PropTypes.array,
	pageSize: PropTypes.number,
	placeholder: PropTypes.string,
	screenReaderStatus: PropTypes.func,
	styles: PropTypes.shape({
		clearIndicator: PropTypes.func,
		container: PropTypes.func,
		control: PropTypes.func,
		dropdownIndicator: PropTypes.func,
		group: PropTypes.func,
		groupHeading: PropTypes.func,
		indicatorsContainer: PropTypes.func,
		indicatorSeparator: PropTypes.func,
		input: PropTypes.func,
		loadingIndicator: PropTypes.func,
		loadingMessageCSS: PropTypes.func,
		menu: PropTypes.func,
		menuList: PropTypes.func,
		menuPortal: PropTypes.func,
		multiValue: PropTypes.func,
		multiValueLabel: PropTypes.func,
		multiValueRemove: PropTypes.func,
		noOptionsMessageCSS: PropTypes.func,
		option: PropTypes.func,
		placeholder: PropTypes.func,
		singleValue: PropTypes.func,
		valueContainer: PropTypes.func,
	}),
	tabIndex: PropTypes.string,
	tabSelectsValue: PropTypes.bool,
	value: PropTypes.oneOfType([PropTypes.object, PropTypes.array]),
};
