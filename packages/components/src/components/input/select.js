/**
 * External dependencies
 */
import { debounce, isEmpty, isArray } from 'lodash';

/**
 * WordPress dependencies
 */
import { forwardRef, useCallback, useEffect, useRef, useState } from '@wordpress/element';
import { useMergeRefs, useInstanceId } from '@wordpress/compose';
import { Icon, search } from '@wordpress/icons';
import { __, spintf } from '@wordpress/i18n';

/**
 * Internal dependencies
 */
import useAsync from './use-async';
import { normalizeValue } from './utils';

const Select = forwardRef((selectProps, ref) => {
	const props = useAsync(selectProps);
	const {
		// basic props.
		id: propsId,
		className,
		label,
		name,
		placeholder,
		help,
		required,

		// customizations.
		isMulti = false,
		isDisabled,
		isSearchable = true,
		maxSelections,
		loadingMessage,
		createMessage,
		creatingMessage,
		noResultsMessage,

		// options.
		getOptionLabel,
		getOptionValue,
		isOptionDisabled,
		renderOption,
		renderSelection,

		// state props.
		defaultValue,
		value: propsValue,
		onChange,
		IsMenuOpen: propsIsMenuOpen,
		// onMenuOpen: propsOnMenuOpen,
		// onMenuClose: propsOnMenuClose,

		// async props.
		inputValue,
		onInputChange,
		options: propsOptions,
		isLoading,
		setIsLoading,

		// Creatable props.
		onCreate,

		...restSelectProps
	} = props;
	// ==============================
	// States
	// ==============================
	const instanceId = useInstanceId(Select);
	const id = `eac-select-control-${instanceId}` || propsId;
	const [stateValue, setStateValue] = useState(propsValue || defaultValue);
	const [stateIsMenuOpen, setStateIsMenuOpen] = useState(propsIsMenuOpen || false);
	const [focusedOption, setFocusedOption] = useState(null);
	const [focusedValue, setFocusedValue] = useState(null);
	const [isCreating, setIsCreating] = useState(false);
	const value = normalizeValue(propsValue || stateValue, isMulti);
	const isMenuOpen = propsIsMenuOpen || stateIsMenuOpen;

	// ==============================
	// Refs
	// ==============================
	const selectRef = useRef();
	const dropdownRef = useRef();
	const optionsRef = useRef();
	const inputRef = useRef();
	const focusedOptionRef = useRef();
	const mergedRef = useMergeRefs([ref, selectRef]);
	// ==============================
	// Consumer Handlers
	// ==============================
	const onMenuOpen = () => {
		if (typeof propsOnMenuOpen === 'function') {
			props.onMenuOpen?.();
		}
		setStateIsMenuOpen(true);
	};
	const onMenuClose = () => {
		onInputChange('');
		if (typeof propsOnMenuClose === 'function') {
			props.onMenuClose?.();
		}
		setStateIsMenuOpen(false);
	};
	// ==============================
	// Methods
	// ==============================
	const isSingleValue = !isMulti || maxSelections === 1;
	const isCreatable = typeof onCreate === 'function';
	const hasValue = !isEmpty(value);
	const hasInputFocus = inputRef?.current === inputRef?.current?.ownerDocument.activeElement;
	const openMenu = () => {
		const options = getFocusAbleOptions();
		let openAtIndex = 0;
		if (isMulti && value && value.length) {
			const selectedIndex = options.findIndex(value[0]);
			if (selectedIndex > -1) {
				openAtIndex = selectedIndex;
			}
		}
		setFocusedValue(null);
		setFocusedOption(options[openAtIndex]);
		onMenuOpen();
	};
	const focusInput = () => inputRef.current.focus();
	const focusOption = (option) => {
		setFocusedValue(null);
		setFocusedOption(option);
	};
	const focusValue = (value) => {
		setFocusedOption(null);
		setFocusedValue(value);
	};
	const getFocusAbleOptions = () => {
		return propsOptions.filter((option) => !isOptionDisabled(option) && !isOptionSelected(option));
	};
	const isOptionSelected = (option) => {
		if (!value) return false;
		if (value.indexOf(option) > -1) return true;
		const candidate = getOptionValue(option);
		return value.some((i) => getOptionValue(props, i) === candidate);
	};
	const onSelectOption = (option) => {
		if (
			isOptionDisabled(option) ||
			(maxSelections && value.length >= maxSelections) ||
			isOptionSelected(option)
		) {
			return;
		}
		if (isMulti) {
			onChange(value ? [...value, option] : [option]);
		} else {
			onChange(option);
		}
	};
	const handleInputChange = (event) => {
		const newInputValue = event.currentTarget.value;
		onInputChange(newInputValue);
		if (!isMenuOpen) {
			openMenu();
		}
	};
	const handleInputBlur = () => {
		// eslint-disable-next-line @wordpress/no-global-active-element
		if (optionsRef && optionsRef.contains(document.activeElement)) {
			focusInput();
			return;
		}

		onInputChange('');
		onMenuClose();
		setFocusedOption(null);
		setFocusedValue(null);
	};
	const handleInputFocus = () => {
		openMenu();
	};

	// ==============================
	// Hooks
	// ==============================
	useEffect(() => {
		if (isMenuOpen && !hasInputFocus) {
			focusInput();
		}
	}, [isMenuOpen, hasInputFocus]);

	// ==============================
	// Renderers
	// ==============================
	const renderSearch = () => {
		if (!isSearchable) return null;
		const inputAttributes = {
			'aria-autocomplete': 'list',
			'aria-expanded': isMenuOpen,
			'aria-haspopup': true,
			'aria-label': __('Search', 'wp-ever-accounting'),
			'aria-required': required,
			role: 'combobox',
			...(!isSearchable && {
				'aria-readonly': true,
			}),
			...(isMenuOpen && {
				'aria-controls': `eac-select-control-${instanceId}-listbox`,
			}),
		};

		return (
			<div className="react-select__search">
				<span className="eac-select-control__search-icon" aria-hidden="true">
					<Icon icon={search} size={18} />
				</span>
				<input
					id={`eac-select-control-${instanceId}-input`}
					className="react-select__search-input"
					placeholder={__('Search', 'wp-ever-accounting')}
					ref={inputRef}
					type="text"
					autoComplete="off"
					autoCorrect="off"
					spellCheck="false"
					tabIndex={0}
					onBlur={handleInputBlur}
					onChange={handleInputChange}
					onFocus={handleInputFocus}
					value={inputValue}
					{...inputAttributes}
				/>
			</div>
		);
	};
	return <>{renderSearch()}</>;
});
export default Select;
