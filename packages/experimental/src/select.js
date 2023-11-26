/**
 * External dependencies
 */
import propTypes from 'prop-types';
import classnames from 'classnames';
import { Tag, useOutsideClick } from '@eac/components';

/**
 * WordPress dependencies
 */
import { forwardRef, useRef, useState, useEffect } from '@wordpress/element';
import { Icon, closeSmall, chevronDown, search } from '@wordpress/icons';
import { useMergeRefs, useInstanceId } from '@wordpress/compose';
import { ESCAPE, TAB, ENTER } from '@wordpress/keycodes';
import { Spinner, BaseControl } from '@wordpress/components';
import { __, sprintf } from '@wordpress/i18n';

/**
 * Internal dependencies
 */
import './style.scss';
import useAsync from './use-async';
import useStateManager from './use-state-manager';

const Select = forwardRef((props, ref) => {
	const asyncProps = useAsync(props);
	const stateManager = useStateManager(asyncProps);
	const {
		id: idProp,
		className,
		label,
		help,
		variant = 'normal',
		getOptionLabel,
		getOptionValue,
		isDisabled,
		isSearchable = true,
		isClearable = true,
		isMulti = true,
		isOptionDisabled,
		loadingMessage,
		noOptionsMessage,
		createMessage,
		placeholder,
		maxSelections,
		required,
		// stateManager
		value,
		options,
		isLoading,
		menuIsOpen,
		onMenuOpen,
		onMenuClose,
		onChange,
		inputValue,
		onInputChange,
		onCreate,
	} = stateManager;

	// ==============================
	// States
	// ==============================
	const instanceId = useInstanceId(Select);
	const id = `eac-select-control-${instanceId}` || idProp;
	const [focusedOption, setFocusedOption] = useState(null);
	const [isCreating, setIsCreating] = useState(false);
	// ==============================
	// Refs
	// ==============================
	const selectRef = useRef();
	const menuRef = useRef();
	const inputRef = useRef();
	const focusedOptionRef = useRef();
	useOutsideClick(selectRef, onMenuClose, onMenuOpen);
	// ==============================
	// Methods
	// ==============================
	const isSingleValue = !isMulti || maxSelections === 1;
	const hasValue = value && value.length > 0;
	const onKeyDown = (event) => {
		if (isDisabled) {
			return;
		}
		if (ENTER === event.keyCode) {
			event.preventDefault();
		}
		if (event.keyCode !== ESCAPE && event.keyCode !== TAB && !event.shiftKey) {
			openMenu();
		}
	};
	const focusValue = () => {};
	const openMenu = () => {
		onMenuOpen();
		focusInput();
	};
	const closeMenu = () => {
		onMenuClose();
	};
	const handleInputFocus = () => {};
	const handleInputBlur = () => {};
	const hasInputFocus = inputRef?.current === inputRef?.current?.ownerDocument.activeElement;
	const focusInput = () => {
		inputRef.current?.focus();
		// make fake event to trigger focus.
		const event = new Event('mousedown', { bubbles: true });
		inputRef.current?.dispatchEvent(event);

	};
	const handleInputChange = (event) => {
		const newInputValue = event.target.value;
		onInputChange(newInputValue);
	};
	const onOptionHover = (option) => {
		if (!option || focusedOption === option) {
		}
		const focusableOptions = getFocusAbleOptions();
		const focusedOptionIndex = focusableOptions.indexOf(option);
		setFocusedOption(focusedOptionIndex > -1 ? option : null);
	};
	const getOptions = () => {
		if (isLoading || !options) {
			return [];
		}

		return options.filter((option) => !isOptionSelected(option));
	};
	const onSelectOption = (option) => {
		if (
			isOptionDisabled(option) ||
			(maxSelections && value.length >= maxSelections) ||
			isOptionSelected(option)
		) {
			return;
		}
		console.log('onSelectOption', option);
		if (isMulti) {
			onChange(value ? [...value, option] : [option]);
		} else {
			onChange(option);
		}
	};
	const isOptionSelected = (option) => {
		if (!value) return false;
		if (value.indexOf(option) > -1) return true;
		const candidate = getOptionValue(option);
		return value.some((i) => getOptionValue(props, i) === candidate);
	};
	const getFocusAbleOptions = () => {
		return options.filter((option) => !isOptionDisabled(option) && !isOptionSelected(option));
	};
	const handleCreateOption = async (newOptions) => {
		setIsCreating(true);
		await onCreate(newOptions, (createdOption) => {
			setIsCreating(false);
			if (createdOption) {
				onSelectOption(createdOption);
			}
		});
	};
	const removeValue = (option) => {
		const candidate = props.getOptionValue(option);
		const nextValue = value.filter((v) => props.getOptionValue(v) !== candidate);
		const newValue = isMulti ? nextValue : nextValue[0] || null;
		onChange(newValue);
		focusInput();
	};
	// ==============================
	// Hooks
	// ==============================
	useEffect(() => {
		if (menuIsOpen && !hasInputFocus) {
			focusInput();
		}
	}, [menuIsOpen, hasInputFocus]);

	// ==============================
	// Renderers
	// ==============================
	const renderSearch = () => {
		if (!menuIsOpen || !isSearchable) {
			return null;
		}
		const inputAttributes = {
			'aria-autocomplete': 'list',
			'aria-expanded': menuIsOpen,
			'aria-haspopup': true,
			role: 'combobox',
			...(!isSearchable && {
				'aria-readonly': true,
			}),
		};

		const spinnerAttributes = {
			'aria-label': __('Loading…', 'wp-ever-accounting'),
			'aria-hidden': true,
		};

		const clearAttributes = {
			'aria-label': __('Clear value', 'wp-ever-accounting'),
			'aria-hidden': true,
			onMouseDown: (event) => {
				event.stopPropagation();
				event.preventDefault();
				onInputChange('');
				focusInput();
			},
		};

		return (
			<div className="eac-select-control__search">
				<span className="eac-select-control__search-icon">
					<Icon icon={search} size={18} />
				</span>
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
					{...inputAttributes}
				/>
				{isLoading && (
					<span className="eac-select-control__spinner" {...spinnerAttributes}>
						<Spinner size={18} />
					</span>
				)}
				{inputValue && (
					<button className="eac-select-control__clear" {...clearAttributes}>
						<Icon icon={closeSmall} size={18} />
					</button>
				)}
			</div>
		);
	};
	const renderOptions = () => {
		if (!options || !options.length) {
			return null;
		}

		return (
			<ul className="eac-select-control__options">
				{getOptions().map((option, index) => {
					// const isFocusedOption = focusedOption === index;
					const optionId = `${instanceId}-option-${getOptionValue(option) || index}`;
					const isFocusedOption = focusedOption === option;
					const onHover = isDisabled ? undefined : () => onOptionHover(option);
					const onSelect = isDisabled ? undefined : () => onSelectOption(option);
					const optionProps = {
						id: optionId,
						className: classnames('eac-select-control__option', {
							'eac-select-control__option--focused': isFocusedOption,
							'eac-select-control__option--selected': isOptionSelected(option),
							'eac-select-control__option--disabled': isOptionDisabled(option),
						}),
						onClick: onSelect,
						onMouseMove: onHover,
						onMouseOver: onHover,
						'aria-disabled': isOptionDisabled(option),
						'aria-selected': value && isOptionSelected(option),
						key: getOptionValue(option) || index,
						// onMouseDown: (event) => {
						// 	event.preventDefault();
						// 	event.stopPropagation();
						// 	if (isOptionDisabled(option)) {
						// 		return;
						// 	}
						// 	if (isMulti) {
						// 		if (value.includes(option)) {
						// 			onChange(value.filter((val) => val !== option));
						// 		} else {
						// 			onChange([...value, option]);
						// 		}
						// 	} else {
						// 		onChange(option);
						// 	}
						// },
						// onMouseEnter: () => {
						// 	focusedOptionRef.current = index;
						// },
						role: 'option',
						tabIndex: -1,
						ref: isFocusedOption ? focusedOptionRef : null,
					};

					// const isOptionFocused = focusedOptionRef.current === index;
					// const isOptionSelected = value && value.includes(option);
					// const isOptionItemDisabled = isOptionDisabled(option);
					// const optionClasses = classnames('eac-select-control__option', {
					// 	'eac-select-control__option--focused': isOptionFocused,
					// 	'eac-select-control__option--selected': isOptionSelected,
					// 	'eac-select-control__option--disabled': isOptionItemDisabled,
					// });
					return (
						<li key={getOptionValue(option) || index} {...optionProps}>
							{getOptionLabel(option)}
						</li>
					);
				})}
			</ul>
		);
	};
	const renderFormField = () => {
		if (required && !hasValue && !isDisabled) {
			return (
				<div className="eac-select-control__required">
					{__('This field is required.', 'wp-ever-accounting')}
				</div>
			);
		}

		if (!name || isDisabled) return;
		if (isMulti) {
			const input = value.map((val, i) => (
				<input key={`i-${i}`} type="hidden" name={`${name}`} value={getOptionValue(val)} />
			));

			return <div className="eac-select-control__hidden">{input}</div>;
		}
		const singleValue = value[0] ? getOptionValue(value[0]) : '';
		return <input type="hidden" name={`${name}`} value={singleValue} />;
	};
	const classes = classnames('eac-select-control', className, {
		'eac-select-control--disabled': isDisabled,
		'eac-select-control--loading': isLoading,
		'eac-select-control--multi': isMulti,
		'eac-select-control--single': !isMulti,
		'eac-select-control--searchable': isSearchable,
		'eac-select-control--clearable': isClearable,
		'eac-select-control--open': menuIsOpen,
		'eac-select-control--empty': !value || value.length === 0,
		[`eac-select-control--${variant}`]: variant,
	});

	return (
		<BaseControl id={id} label={label} help={help}>
			<div
				id={id}
				className={classes}
				role="combobox"
				aria-expanded={menuIsOpen}
				aria-haspopup="listbox"
				onKeyDown={onKeyDown}
				onFocus={focusValue}
				tabIndex="0"
				ref={useMergeRefs([selectRef, ref])}
			>
				<div
					className="eac-select-control__container"
					onMouseDown={openMenu}
					onKeyDown={openMenu}
					aria-live="polite"
					aria-atomic="true"
					role="combobox"
					tabIndex="-1"
				>
					{!hasValue && placeholder && (
						<div className="eac-select-control__placeholder">{placeholder}</div>
					)}

					{!isMulti && hasValue && (
						<div className="eac-select-control__value">{getOptionLabel(value)}</div>
					)}

					{isMulti && hasValue && (
						<div className="eac-select-control__selections">
							{value.map((val, index) => {
								return (
									<Tag
										key={getOptionValue(val) || index}
										className="eac-select-control__value-item"
										onRemove={() => removeValue(val)}
										label={getOptionLabel(val)}
									/>
								);
							})}
						</div>
					)}

					{(!isMulti || !hasValue) && variant !== 'inline' && (
						<span className="eac-select-control__indicator">
							<Icon icon={chevronDown} size={18} />
						</span>
					)}
				</div>
				{menuIsOpen && (
					<div className="eac-select-control__dropdown">
						{renderSearch()}
						<div className="eac-select-control__results">
							{renderOptions()}

							{!options.length && !isLoading && typeof onCreate === 'function' && (
								<div className="eac-select-control__option--create">
									<span
										className="eac-select-control__create-button"
										onClick={handleCreateOption}
										onKeyDown={handleCreateOption}
										role="button"
										tabIndex="0"
									>
										{createMessage(inputValue)}
										{isCreating && <Spinner size={18} />}
									</span>
								</div>
							)}

							{!options.length && isLoading && (
								<div className="eac-select-control__option--loading">
									{loadingMessage()}
								</div>
							)}

							{!options.length && !isLoading && (
								<div className="eac-select-control__option--no-results">
									{noOptionsMessage()}
								</div>
							)}
						</div>
					</div>
				)}

				{renderFormField()}
			</div>
		</BaseControl>
	);
});

Select.propTypes = {
	className: propTypes.string,
	id: propTypes.string,
	label: propTypes.string,
	help: propTypes.string,
	variant: propTypes.oneOf(['normal', 'inline']),
	getOptionLabel: propTypes.func,
	getOptionValue: propTypes.func,
	isDisabled: propTypes.bool,
	isLoading: propTypes.bool,
	isMulti: propTypes.bool,
	isSearchable: propTypes.bool,
	isClearable: propTypes.bool,
	loadOptions: propTypes.func,
	options: propTypes.array,
	isOptionDisabled: propTypes.func,
	loadingMessage: propTypes.func,
	noOptionsMessage: propTypes.func,
	createMessage: propTypes.func,
	placeholder: propTypes.string,
	maxSelections: propTypes.number,
	onChange: propTypes.func,
	onCreate: propTypes.func,
	onInputChange: propTypes.func,
	required: propTypes.bool,
};

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
	isSearchable: true,
	isClearable: true,
	isMulti: false,
	loadOptions: null,
	options: [],
	isOptionDisabled: (option) => (option && option.disabled) || false,
	loadingMessage: () => __('Loading…', 'wp-ever-accounting'),
	noOptionsMessage: () => __('No options', 'wp-ever-accounting'),
	createMessage: (inputVal) =>
		sprintf(
			// translators: %s: input value
			__('Create "%s"', 'wp-ever-accounting'),
			inputVal
		),
	placeholder: __('Select…', 'wp-ever-accounting'),
	maxSelections: null,
	onChange: null,
	onCreate: () => {},
	onInputChange: null,
	required: false,
};

Select.displayName = 'Select';

export default Select;
