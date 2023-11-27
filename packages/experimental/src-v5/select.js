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
import { Button, Spinner, BaseControl } from '@wordpress/components';
import { compose } from '@wordpress/compose';
import { Icon, closeSmall, chevronDown } from '@wordpress/icons';
/**
 * Internal dependencies
 */
import './style.scss';
import useAsync from './use-async';
import useStateManager from './use-state-manager';
import Tag from './tag';
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
		<BaseControl
			label={__('Select', 'wp-ever-accounting')}
			help={__('Select help', 'wp-ever-accounting')}
		>
			<div
				className="eac-select-control eac-select-control--inline"
				ref={containerRef}
				role="combobox"
				aria-expanded={menuIsOpen}
				aria-haspopup="listbox"
				tabIndex="0"
			>
				<div className="eac-select-control__container">
					<div className="eac-select-control__selections">
						<Tag label="Option 1" remove={() => {}} />
						<Tag label="Option 1" remove={() => {}} />
						<Tag label="Option 1" remove={() => {}} />
						<Tag label="Option 1" remove={() => {}} />
						<Tag label="Option 1" remove={() => {}} />
						<Tag label="Option 1" remove={() => {}} />
					</div>
					<span className="eac-select-control__indicator">
						<Icon icon={chevronDown} size={18} />
					</span>
				</div>
				<div className="eac-select-control__dropdown">
					<div className="eac-select-control__search">
						<input
							className="eac-select-control__input"
							ref={inputRef}
							type="text"
							value={inputValue}
							onChange={handleInputChange}
							onFocus={handleInputFocus}
							onBlur={handleInputBlur}
							placeholder={placeholder}
							autoComplete="off"
							autoCorrect="off"
							autoCapitalize="off"
							spellCheck="false"
							aria-autocomplete="list"
							aria-controls="react-select-2"
							aria-activedescendant="react-select-2--value"
							aria-labelledby="react-select-2--value"
							aria-expanded="true"
							aria-haspopup="true"
							aria-owns="react-select-2--list"
							aria-readonly="false"
							aria-busy="false"
							aria-disabled="false"
							role="combobox"
						/>
						<span className="eac-select-control__spinner">
							<Spinner size={18} />
						</span>
						<button className="eac-select-control__clear">
							<Icon icon={closeSmall} size={18} />
						</button>
					</div>
					<div className="eac-select-control__results">
						<ul className="eac-select-control__options">
							<li className="eac-select-control__option">Option 1</li>
							<li className="eac-select-control__option">Option 2</li>
							<li className="eac-select-control__option">Option 3</li>
							<li className="eac-select-control__option">Option 3</li>
							<li className="eac-select-control__option">Option 3</li>
							<li className="eac-select-control__option">Option 3</li>
							<li className="eac-select-control__option">Option 3</li>
							<li className="eac-select-control__option">Option 3</li>
							<li className="eac-select-control__option">Option 3</li>
							<li className="eac-select-control__option">Option 3</li>
							<li className="eac-select-control__option">Option 3</li>
							<li className="eac-select-control__option">Option 3</li>
						</ul>
					</div>
				</div>
			</div>
		</BaseControl>
	);
}

Select.propTypes = {};

Select.defaultProps = {
	className: null,
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
