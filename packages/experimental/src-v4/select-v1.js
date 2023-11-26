/**
 * External dependencies
 */
import propTypes from 'prop-types';
import classnames from 'classnames';

/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { useRef, useState, useEffect } from '@wordpress/element';
import { compose } from '@wordpress/compose';

/**
 * Internal dependencies
 */
import useAsync from './use-async';
import useStateManager from './use-state-manager';
import { SelectContainer } from './styles';
import Menu from './menu';

function Select(props) {
	const asyncProps = useAsync(props);
	const stateManager = useStateManager(asyncProps);
	const {
		isDisabled,
		isLoading,
		isMulti,
		options,
		onInputChange,
		value,
		variant,
		menuIsOpen,
		onMenuOpen,
		onMenuClose,
	} = stateManager;
	const [isFocused, setIsFocused] = useState(false);
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
	const closeDropdown = () => dropdownRef?.current?.close();
	const onFocusContainer = () => {
		//containerRef?.current?.focus();
		inputRef?.current?.focus();
		onMenuOpen();
	};
	const onBlurContainer = () => {
		// check if the blur event is caused by clicking on the dropdown menu or not.
		// if it is, don't close the menu.
		// containerRef?.current?.blur();
		// onMenuClose();
	};
	const getOptionLabel = (option) => {
		if (props.getOptionLabel) {
			return props.getOptionLabel(option, props);
		}

		return option.label;
	};
	const getOptionValue = (option) => {
		if (props.getOptionValue) {
			return props.getOptionValue(option, props);
		}

		return option.value;
	};

	// ==============================
	// Hooks
	// ==============================
	// if loadOptions is provided, useAsync will handle loading options.
	// otherwise, options must be provided.
	// console.log(isLoading, options);

	// using useEffect, focusInput will be called when menuIsOpen is true and isSearchable is true.
	useEffect(() => {
		if (menuIsOpen && props.isSearchable) {
			focusInput();
		}
	}, [menuIsOpen, props.isSearchable]);

	// ==============================
	// Render
	// ==============================

	const renderOption = (option) => {
		if (props.propsRenderOption) {
			return props.propsRenderOption(option);
		}

		return option.label;
	};

	const className = classnames('eac-select-control', {
		'eac-select-control--disabled': isDisabled,
		'eac-select-control--focused': isFocused,
		'eac-select-control--loading': isLoading,
		'eac-select-control--multi': isMulti,
		'eac-select-control--single': !isMulti,
		'eac-select-control--open': menuIsOpen,
		// 'eac-select-control--empty': isValueEmpty,
		// [`eac-select-control--${variant}`]: variant,
	});

	return (
		<SelectContainer
			className={className}
			variant={variant}
			isDisabled={isDisabled}
			// isFocused={isFocused}
			onFocus={onFocusContainer}
			onBlur={onBlurContainer}
			tabIndex="0"
			ref={containerRef}
			area-expanded={isFocused}
		>
			lorem ipsum
			{menuIsOpen && !isDisabled && (
				<Menu ref={dropdownRef} inputRef={inputRef} {...stateManager} />
			)}
		</SelectContainer>
	);
}

Select.propTypes = {};

Select.defaultProps = {
	// classNames = '',
	// id = '',
	// label = '',
	// help,
	variant: 'normal',
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
