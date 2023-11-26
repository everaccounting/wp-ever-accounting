/**
 * External dependencies
 */
import propTypes from 'prop-types';

/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { useRef } from '@wordpress/element';

/**
 * Internal dependencies
 */
import useAsync from './use-async';

function Select(props) {
	// ==============================
	// Refs
	// ------------------------------
	const containerRef = useRef();
	const dropdownRef = useRef();
	const inputRef = useRef();

	// ==============================
	// Methods
	// ==============================
	const focusInput = () => inputRef?.current?.focus();
	const blurInput = () => inputRef?.current?.blur();
	const openDropdown = () => dropdownRef?.current?.open();

	// ==============================
	// Hooks
	// ------------------------------
	// if loadOptions is provided, useAsync will handle loading options.
	// otherwise, options must be provided.
	const async = useAsync(props);

	console.log(props);
	console.log(async);
}
Select.propTypes = {};

Select.defaultProps = {
	// classNames = '',
	// id = '',
	// label = '',
	// help,
	getOptionLabel: (option) => (option && option.label) || '',
	getOptionValue: (option) => (option && option.value) || '',
	isDisabled: false,
	isLoading: false,
	isMulti: false,
	isSearchable: true,
	isClearable: true,
	loadOptions: null,
	options: [],
	isOptionDisabled: (option) => (option && option.disabled) || false,
	loadingMessage: () => __('Loading…', 'wp-ever-accounting'),
	noOptionsMessage: () => __('No options', 'wp-ever-accounting'),
	placeholder: __('Select…', 'wp-ever-accounting'),
	maxSelections: null,
	onChange: () => {},
	onCreate: () => {},
	onInputChange: () => {},
	required: false,
};

export default Select;
