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
	const renderInput = () => {
		if (!isSearchable) {
			return null;
		}
		const ariaAttributes = {
			'aria-autocomplete': 'list',
			'aria-expanded': menuIsOpen,
			'aria-haspopup': true,
			role: 'combobox',
			...(!isSearchable && {
				'aria-readonly': true,
			}),
		};

		return (
			<input
				className="eac-select-control__input"
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
				{...ariaAttributes}
			/>
		);
	};
	const renderClearIcon = () => {
		// if (isDisabled || !isClearable || !inputValue || !value || !value.length) {
		// 	return null;
		// }
		const innerProps = {
			'aria-hidden': true,
			className: 'eac-select-control__input-icon eac-select-control__input-icon--clear',
			onMouseDown: (event) => {
				event.stopPropagation();
				event.preventDefault();
				onInputChange('');
			},
		};

		return (
			<span {...innerProps}>
				<Icon icon={closeSmall} />
			</span>
		);
	};
	const renderLoadingIcon = () => {
		if (!isLoading) {
			return null;
		}
		const innerProps = {
			'aria-hidden': true,
			className: 'eac-select-control__input-icon eac-select-control__input-icon--loading',
		};

		return (
			<div {...innerProps}>
				<Spinner />
			</div>
		);
	};
	const renderInputContainer = () => {
		return (
			<div
				className="eac-select-control__input-container"
				ref={menuRef}
				aria-hidden={!menuIsOpen}
			>
				{renderInput()}
				<div className="eac-select-control__input-icons">
					{renderClearIcon()}
					{renderLoadingIcon()}
				</div>
			</div>
		);
	};
	const renderMenu = () => {
		let menuUI;
		if (options && options.length > 0) {
			menuUI = (
				<Option
					{...stateManager}
					inputRef={inputRef}
					// focusedOptionRef={focusedOptionRef}
					// focusedOption={focusedOption}
					// onFocusOption={setFocusedOption}
					// onSelectOption={selectOption}
					// onRemoveValue={removeValue}
					// onClearValue={clearValue}
					// onCreateOption={createOption}
				/>
			);
		} else if (isLoading) {
			const message = props.loadingMessage({ inputValue });
			if (message === null) return null;
			menuUI = <div className="eac-select-control__loading">{message}</div>;
		} else if (!isLoading && inputValue && typeof props.onCreate === 'function') {
			const message = props.createMessage({ inputValue });
			if (message === null) return null;
			menuUI = (
				<div
					className="eac-select-control__create-option"
					onClick={handleCreateOption}
					tabIndex="0"
				>
					{message}
				</div>
			);
		} else {
			const message = props.noOptionsMessage({ inputValue });
			if (message === null) return null;
			menuUI = <div className="eac-select-control__no-options">{message}</div>;
		}

		return (
			<div className="eac-select-control__menu" ref={menuRef}>
				{isSearchable && renderInputContainer()}
				{menuUI}
			</div>
		);
	};

	return (
		<div
			className="eac-select-control"
			ref={containerRef}
			role="combobox"
			aria-expanded={menuIsOpen}
			aria-haspopup="listbox"
			onKeyDown={onKeyDown}
			tabIndex="0"
		>
			{renderMenu()}
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
