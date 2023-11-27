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
import { Button, Spinner } from '@wordpress/components';
import { compose } from '@wordpress/compose';
import { Icon, closeSmall } from '@wordpress/icons';

/**
 * Internal dependencies
 */
import './style.scss';
import useAsync from './use-async';
import useStateManager from './use-state-manager';
import { SelectContainer, InputContainer, Options, Option } from './styles';

function Select(props) {
	const asyncProps = useAsync(props);
	const stateManager = useStateManager(asyncProps);
	const {
		value,
		options,
		isDisabled,
		isLoading,
		isSearchable,
		isClearable,
		isMulti,
		menuIsOpen,
		placeholder,
		onChange,
		inputValue,
		onInputChange,
		onCreate,
	} = stateManager;

	// ==============================
	// States
	// ------------------------------
	const [isFocused, setIsFocused] = useState(false);
	const [focusedOption, setFocusedOption] = useState(null);

	// ==============================
	// Refs
	// ==============================
	const containerRef = useRef();
	const menuRef = useRef();
	const inputRef = useRef();
	const focusedOptionRef = useRef();

	// ==============================
	// Methods
	// ==============================
	const focusInput = () => inputRef?.current?.focus();
	const openMenu = (focusOption) => {};
	const focusValue = () => {};
	const focusOption = () => {};
	const setValue = (newValue) => {
		onInputChange('');
		// handle both of single and multi select.
		if (isMulti) {
			const nextValue = Array.isArray(newValue) ? newValue : [];
			return onChange(nextValue);
		}

		return onChange(newValue);
	};
	const selectOption = (newValue) => {
		const deselected = isMulti && value && value.includes(newValue);
		const isDisabledOption = props.isOptionDisabled(newValue);
		if (deselected) {
			const nextValue = value.filter((v) => v !== newValue);
			setValue(nextValue);
		}
	};
	const removeValue = (removedValue) => {
		const candidate = props.getOptionValue(removedValue);
		const nextValue = value.filter((v) => props.getOptionValue(v) !== candidate);
		const newValue = isMulti ? nextValue : nextValue[0] || null;
		onChange(newValue);
		focusInput();
	};
	const clearValue = () => {
		const newValue = isMulti ? [] : null;
		onChange(newValue);
	};
	const handleInputBlur = () => {};
	const handleInputFocus = () => {};
	const handleInputChange = (event) => {
		const newInputValue = event.target.value;
		onInputChange(newInputValue);
	};
	const handleClearValue = (event) => {
		event.stopPropagation();
		event.preventDefault();
		clearValue();
	};
	const handleCreateOption = (event) => {};

	// ==============================
	// Renderers
	// ==============================
	return (
		<div
			className="eac-select-control"
			ref={containerRef}
			role="combobox"
			aria-expanded={menuIsOpen}
			aria-haspopup="listbox"
			tabIndex="0"
		>
			<div className="eac-select-control__container"></div>
			<div className="eac-select-control__dropdown">
				<div className="eac-select-control__input">
					<input
						className="eac-select-control__input-input"
						ref={inputRef}
						autoCapitalize="none"
						autoComplete="off"
						autoCorrect="off"
						disabled={isDisabled}
						onFocus={handleInputFocus}
						onBlur={handleInputBlur}
						onChange={handleInputChange}
						type="text"
						spellCheck="false"
						tabIndex={0}
						value={inputValue}
					/>
					<div className="eac-select-control__input-icon">
						<Spinner />
					</div>
					<div
						aria-hidden="true"
						className="eac-select-control__input-icon"
						onMouseDown={handleClearValue}
					>
						<Icon icon={closeSmall} />
					</div>
				</div>
				<div className="eac-select-control__menu">
					<div className="eac-select-control__menu-options">
						<div className="eac-select-control__menu-option-label">Option 1</div>
					</div>
				</div>
			</div>
		</div>
	);
}

Select.propTypes = {};

Select.defaultProps = {
	// className = '',
	id: '',
	label: '',
	help: '',
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
	createMessage: () => __('Create', 'wp-ever-accounting'),
	placeholder: __('Select…', 'wp-ever-accounting'),
	maxSelections: null,
	onChange: null,
	onCreate: null,
	onInputChange: null,
	required: false,
};

export default Select;
