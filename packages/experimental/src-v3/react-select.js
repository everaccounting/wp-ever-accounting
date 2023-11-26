/**
 * External dependencies
 */
import * as React from 'react';
import { Component } from 'react';
/**
 * Internal dependencies
 */
import { MenuPlacer } from './components/Menu';
import LiveRegion from './components/LiveRegion';

import { createFilter } from './filters';
import { DummyInput, ScrollManager, RequiredInput } from './internal/index';
import { isAppleDevice } from './accessibility/helpers';

import {
	classNames,
	cleanValue,
	isTouchCapable,
	isMobileDevice,
	noop,
	scrollIntoView,
	isDocumentElement,
	notNullish,
	valueTernary,
	multiValueAsValue,
	singleValueAsValue,
} from './utils';

import {
	formatGroupLabel as formatGroupLabelBuiltin,
	getOptionLabel as getOptionLabelBuiltin,
	getOptionValue as getOptionValueBuiltin,
	isOptionDisabled as isOptionDisabledBuiltin,
} from './builtins';

import { defaultComponents } from './components/index';

import { defaultStyles } from './styles';
import { defaultTheme } from './theme';

export const defaultProps = {
	'aria-live': 'polite',
	backspaceRemovesValue: true,
	blurInputOnSelect: isTouchCapable(),
	captureMenuScroll: !isTouchCapable(),
	classNames: {},
	closeMenuOnSelect: true,
	closeMenuOnScroll: false,
	components: {},
	controlShouldRenderValue: true,
	escapeClearsValue: false,
	filterOption: createFilter(),
	formatGroupLabel: formatGroupLabelBuiltin,
	getOptionLabel: getOptionLabelBuiltin,
	getOptionValue: getOptionValueBuiltin,
	isDisabled: false,
	isLoading: false,
	isMulti: false,
	isRtl: false,
	isSearchable: true,
	isOptionDisabled: isOptionDisabledBuiltin,
	loadingMessage: () => 'Loading...',
	maxMenuHeight: 300,
	minMenuHeight: 140,
	menuIsOpen: false,
	menuPlacement: 'bottom',
	menuPosition: 'absolute',
	menuShouldBlockScroll: false,
	menuShouldScrollIntoView: !isMobileDevice(),
	noOptionsMessage: () => 'No options',
	openMenuOnFocus: false,
	openMenuOnClick: true,
	options: [],
	pageSize: 5,
	placeholder: 'Select...',
	screenReaderStatus: ({ count }) => `${count} result${count !== 1 ? 's' : ''} available`,
	styles: {},
	tabIndex: 0,
	tabSelectsValue: true,
	unstyled: false,
};

function toCategorizedOption(props, option, selectValue, index) {
	const isDisabled = isOptionDisabled(props, option, selectValue);
	const isSelected = isOptionSelected(props, option, selectValue);
	const label = getOptionLabel(props, option);
	const value = getOptionValue(props, option);

	return {
		type: 'option',
		data: option,
		isDisabled,
		isSelected,
		label,
		value,
		index,
	};
}

function buildCategorizedOptions(props, selectValue) {
	return props.options
		.map((groupOrOption, groupOrOptionIndex) => {
			if ('options' in groupOrOption) {
				const categorizedOptions = groupOrOption.options
					.map((option, optionIndex) =>
						toCategorizedOption(props, option, selectValue, optionIndex)
					)
					.filter((categorizedOption) => isFocusable(props, categorizedOption));
				return categorizedOptions.length > 0
					? {
							type: 'group',
							data: groupOrOption,
							options: categorizedOptions,
							index: groupOrOptionIndex,
					  }
					: undefined;
			}
			const categorizedOption = toCategorizedOption(
				props,
				groupOrOption,
				selectValue,
				groupOrOptionIndex
			);
			return isFocusable(props, categorizedOption) ? categorizedOption : undefined;
		})
		.filter(notNullish);
}

function buildFocusableOptionsFromCategorizedOptions(categorizedOptions) {
	return categorizedOptions.reduce((optionsAccumulator, categorizedOption) => {
		if (categorizedOption.type === 'group') {
			optionsAccumulator.push(...categorizedOption.options.map((option) => option.data));
		} else {
			optionsAccumulator.push(categorizedOption.data);
		}
		return optionsAccumulator;
	}, []);
}

function buildFocusableOptionsWithIds(categorizedOptions, optionId) {
	return categorizedOptions.reduce((optionsAccumulator, categorizedOption) => {
		if (categorizedOption.type === 'group') {
			optionsAccumulator.push(
				...categorizedOption.options.map((option) => ({
					data: option.data,
					id: `${optionId}-${categorizedOption.index}-${option.index}`,
				}))
			);
		} else {
			optionsAccumulator.push({
				data: categorizedOption.data,
				id: `${optionId}-${categorizedOption.index}`,
			});
		}
		return optionsAccumulator;
	}, []);
}

function buildFocusableOptions(props, selectValue) {
	return buildFocusableOptionsFromCategorizedOptions(buildCategorizedOptions(props, selectValue));
}

function isFocusable(props, categorizedOption) {
	const { inputValue = '' } = props;
	const { data, isSelected, label, value } = categorizedOption;

	return (
		(!shouldHideSelectedOptions(props) || !isSelected) &&
		filterOption(props, { label, value, data }, inputValue)
	);
}

function getNextFocusedValue(state, nextSelectValue) {
	const { focusedValue, selectValue: lastSelectValue } = state;
	const lastFocusedIndex = lastSelectValue.indexOf(focusedValue);
	if (lastFocusedIndex > -1) {
		const nextFocusedIndex = nextSelectValue.indexOf(focusedValue);
		if (nextFocusedIndex > -1) {
			// the focused value is still in the selectValue, return it
			return focusedValue;
		} else if (lastFocusedIndex < nextSelectValue.length) {
			// the focusedValue is not present in the next selectValue array by
			// reference, so return the new value at the same index
			return nextSelectValue[lastFocusedIndex];
		}
	}
	return null;
}

function getNextFocusedOption(state, options) {
	const { focusedOption: lastFocusedOption } = state;
	return lastFocusedOption && options.indexOf(lastFocusedOption) > -1
		? lastFocusedOption
		: options[0];
}

const getFocusedOptionId = (focusableOptionsWithIds, focusedOption) => {
	const focusedOptionId = focusableOptionsWithIds.find(
		(option) => option.data === focusedOption
	)?.id;
	return focusedOptionId || null;
};

const getOptionLabel = (props, data) => {
	return props.getOptionLabel(data);
};
const getOptionValue = (props, data) => {
	return props.getOptionValue(data);
};

function isOptionDisabled(props, option, selectValue) {
	return typeof props.isOptionDisabled === 'function'
		? props.isOptionDisabled(option, selectValue)
		: false;
}
function isOptionSelected(props, option, selectValue) {
	if (selectValue.indexOf(option) > -1) return true;
	if (typeof props.isOptionSelected === 'function') {
		return props.isOptionSelected(option, selectValue);
	}
	const candidate = getOptionValue(props, option);
	return selectValue.some((i) => getOptionValue(props, i) === candidate);
}
function filterOption(props, option, inputValue) {
	return props.filterOption ? props.filterOption(option, inputValue) : true;
}

const shouldHideSelectedOptions = (props) => {
	const { hideSelectedOptions, isMulti } = props;
	if (hideSelectedOptions === undefined) return isMulti;
	return hideSelectedOptions;
};

let instanceId = 1;

export default class Select extends Component {
	static defaultProps = defaultProps;
	state = {
		ariaSelection: null,
		focusedOption: null,
		focusedOptionId: null,
		focusableOptionsWithIds: [],
		focusedValue: null,
		inputIsHidden: false,
		isFocused: false,
		selectValue: [],
		clearFocusValueOnUpdate: false,
		prevWasFocused: false,
		inputIsHiddenAfterUpdate: undefined,
		prevProps: undefined,
		instancePrefix: '',
	};

	// Misc. Instance Properties
	// ------------------------------

	blockOptionHover = false;
	isComposing = false;
	initialTouchX = 0;
	initialTouchY = 0;
	openAfterFocus = false;
	scrollToFocusedOptionOnUpdate = false;
	isAppleDevice = isAppleDevice();

	// Refs
	// ------------------------------

	controlRef = null;
	getControlRef = (ref) => {
		this.controlRef = ref;
	};
	focusedOptionRef = null;
	getFocusedOptionRef = (ref) => {
		this.focusedOptionRef = ref;
	};
	menuListRef = null;
	getMenuListRef = (ref) => {
		this.menuListRef = ref;
	};
	inputRef = null;
	getInputRef = (ref) => {
		this.inputRef = ref;
	};

	// Lifecycle
	// ------------------------------

	constructor(props) {
		super(props);
		this.state.instancePrefix = 'react-select-' + (this.props.instanceId || ++instanceId);
		this.state.selectValue = cleanValue(props.value);
		// Set focusedOption if menuIsOpen is set on init (e.g. defaultMenuIsOpen)
		if (props.menuIsOpen && this.state.selectValue.length) {
			const focusableOptionsWithIds = this.getFocusableOptionsWithIds();
			const focusableOptions = this.buildFocusableOptions();
			const optionIndex = focusableOptions.indexOf(this.state.selectValue[0]);
			this.state.focusableOptionsWithIds = focusableOptionsWithIds;
			this.state.focusedOption = focusableOptions[optionIndex];
			this.state.focusedOptionId = getFocusedOptionId(
				focusableOptionsWithIds,
				focusableOptions[optionIndex]
			);
		}
	}

	static getDerivedStateFromProps(props, state) {
		const {
			prevProps,
			clearFocusValueOnUpdate,
			inputIsHiddenAfterUpdate,
			ariaSelection,
			isFocused,
			prevWasFocused,
			instancePrefix,
		} = state;
		const { options, value, menuIsOpen, inputValue, isMulti } = props;
		const selectValue = cleanValue(value);
		let newMenuOptionsState = {};
		if (
			prevProps &&
			(value !== prevProps.value ||
				options !== prevProps.options ||
				menuIsOpen !== prevProps.menuIsOpen ||
				inputValue !== prevProps.inputValue)
		) {
			const focusableOptions = menuIsOpen ? buildFocusableOptions(props, selectValue) : [];

			const focusableOptionsWithIds = menuIsOpen
				? buildFocusableOptionsWithIds(
						buildCategorizedOptions(props, selectValue),
						`${instancePrefix}-option`
				  )
				: [];

			const focusedValue = clearFocusValueOnUpdate
				? getNextFocusedValue(state, selectValue)
				: null;
			const focusedOption = getNextFocusedOption(state, focusableOptions);
			const focusedOptionId = getFocusedOptionId(focusableOptionsWithIds, focusedOption);

			newMenuOptionsState = {
				selectValue,
				focusedOption,
				focusedOptionId,
				focusableOptionsWithIds,
				focusedValue,
				clearFocusValueOnUpdate: false,
			};
		}
		// some updates should toggle the state of the input visibility
		const newInputIsHiddenState =
			inputIsHiddenAfterUpdate != null && props !== prevProps
				? {
						inputIsHidden: inputIsHiddenAfterUpdate,
						inputIsHiddenAfterUpdate: undefined,
				  }
				: {};

		let newAriaSelection = ariaSelection;

		let hasKeptFocus = isFocused && prevWasFocused;

		if (isFocused && !hasKeptFocus) {
			// If `value` or `defaultValue` props are not empty then announce them
			// when the Select is initially focused
			newAriaSelection = {
				value: valueTernary(isMulti, selectValue, selectValue[0] || null),
				options: selectValue,
				action: 'initial-input-focus',
			};

			hasKeptFocus = !prevWasFocused;
		}

		// If the 'initial-input-focus' action has been set already
		// then reset the ariaSelection to null
		if (ariaSelection?.action === 'initial-input-focus') {
			newAriaSelection = null;
		}

		return {
			...newMenuOptionsState,
			...newInputIsHiddenState,
			prevProps: props,
			ariaSelection: newAriaSelection,
			prevWasFocused: hasKeptFocus,
		};
	}
	componentDidMount() {
		this.startListeningComposition();
		this.startListeningToTouch();

		if (this.props.closeMenuOnScroll && document && document.addEventListener) {
			// Listen to all scroll events, and filter them out inside of 'onScroll'
			document.addEventListener('scroll', this.onScroll, true);
		}

		if (this.props.autoFocus) {
			this.focusInput();
		}

		// Scroll focusedOption into view if menuIsOpen is set on mount (e.g. defaultMenuIsOpen)
		if (
			this.props.menuIsOpen &&
			this.state.focusedOption &&
			this.menuListRef &&
			this.focusedOptionRef
		) {
			scrollIntoView(this.menuListRef, this.focusedOptionRef);
		}
	}
	componentDidUpdate(prevProps) {
		const { isDisabled, menuIsOpen } = this.props;
		const { isFocused } = this.state;

		if (
			// ensure focus is restored correctly when the control becomes enabled
			(isFocused && !isDisabled && prevProps.isDisabled) ||
			// ensure focus is on the Input when the menu opens
			(isFocused && menuIsOpen && !prevProps.menuIsOpen)
		) {
			this.focusInput();
		}

		if (isFocused && isDisabled && !prevProps.isDisabled) {
			// ensure select state gets blurred in case Select is programmatically disabled while focused
			// eslint-disable-next-line react/no-did-update-set-state
			this.setState({ isFocused: false }, this.onMenuClose);
		} else if (
			!isFocused &&
			!isDisabled &&
			prevProps.isDisabled &&
			this.inputRef === document.activeElement
		) {
			// ensure select state gets focused in case Select is programatically re-enabled while focused (Firefox)
			// eslint-disable-next-line react/no-did-update-set-state
			this.setState({ isFocused: true });
		}

		// scroll the focused option into view if necessary
		if (this.menuListRef && this.focusedOptionRef && this.scrollToFocusedOptionOnUpdate) {
			scrollIntoView(this.menuListRef, this.focusedOptionRef);
			this.scrollToFocusedOptionOnUpdate = false;
		}
	}
	componentWillUnmount() {
		this.stopListeningComposition();
		this.stopListeningToTouch();
		document.removeEventListener('scroll', this.onScroll, true);
	}

	// ==============================
	// Consumer Handlers
	// ==============================

	onMenuOpen() {
		this.props.onMenuOpen();
	}
	onMenuClose() {
		this.onInputChange('', {
			action: 'menu-close',
			prevInputValue: this.props.inputValue,
		});

		this.props.onMenuClose();
	}
	onInputChange(newValue, actionMeta) {
		this.props.onInputChange(newValue, actionMeta);
	}

	// ==============================
	// Methods
	// ==============================

	focusInput() {
		if (!this.inputRef) return;
		this.inputRef.focus();
	}
	blurInput() {
		if (!this.inputRef) return;
		this.inputRef.blur();
	}

	// aliased for consumers
	focus = this.focusInput;
	blur = this.blurInput;

	openMenu(focusOption) {
		const { selectValue, isFocused } = this.state;
		const focusableOptions = this.buildFocusableOptions();
		let openAtIndex = focusOption === 'first' ? 0 : focusableOptions.length - 1;

		if (!this.props.isMulti) {
			const selectedIndex = focusableOptions.indexOf(selectValue[0]);
			if (selectedIndex > -1) {
				openAtIndex = selectedIndex;
			}
		}

		// only scroll if the menu isn't already open
		this.scrollToFocusedOptionOnUpdate = !(isFocused && this.menuListRef);

		this.setState(
			{
				inputIsHiddenAfterUpdate: false,
				focusedValue: null,
				focusedOption: focusableOptions[openAtIndex],
				focusedOptionId: this.getFocusedOptionId(focusableOptions[openAtIndex]),
			},
			() => this.onMenuOpen()
		);
	}

	focusValue(direction) {
		const { selectValue, focusedValue } = this.state;

		// Only multiselects support value focusing
		if (!this.props.isMulti) return;

		this.setState({
			focusedOption: null,
		});

		let focusedIndex = selectValue.indexOf(focusedValue);
		if (!focusedValue) {
			focusedIndex = -1;
		}

		const lastIndex = selectValue.length - 1;
		let nextFocus = -1;
		if (!selectValue.length) return;

		switch (direction) {
			case 'previous':
				if (focusedIndex === 0) {
					// don't cycle from the start to the end
					nextFocus = 0;
				} else if (focusedIndex === -1) {
					// if nothing is focused, focus the last value first
					nextFocus = lastIndex;
				} else {
					nextFocus = focusedIndex - 1;
				}
				break;
			case 'next':
				if (focusedIndex > -1 && focusedIndex < lastIndex) {
					nextFocus = focusedIndex + 1;
				}
				break;
		}
		this.setState({
			inputIsHidden: nextFocus !== -1,
			focusedValue: selectValue[nextFocus],
		});
	}

	focusOption(direction = 'first') {
		const { pageSize } = this.props;
		const { focusedOption } = this.state;
		const options = this.getFocusableOptions();

		if (!options.length) return;
		let nextFocus = 0; // handles 'first'
		let focusedIndex = options.indexOf(focusedOption);
		if (!focusedOption) {
			focusedIndex = -1;
		}

		if (direction === 'up') {
			nextFocus = focusedIndex > 0 ? focusedIndex - 1 : options.length - 1;
		} else if (direction === 'down') {
			nextFocus = (focusedIndex + 1) % options.length;
		} else if (direction === 'pageup') {
			nextFocus = focusedIndex - pageSize;
			if (nextFocus < 0) nextFocus = 0;
		} else if (direction === 'pagedown') {
			nextFocus = focusedIndex + pageSize;
			if (nextFocus > options.length - 1) nextFocus = options.length - 1;
		} else if (direction === 'last') {
			nextFocus = options.length - 1;
		}
		this.scrollToFocusedOptionOnUpdate = true;
		this.setState({
			focusedOption: options[nextFocus],
			focusedValue: null,
			focusedOptionId: this.getFocusedOptionId(options[nextFocus]),
		});
	}
	onChange = (newValue, actionMeta) => {
		const { onChange, name } = this.props;
		actionMeta.name = name;

		this.ariaOnChange(newValue, actionMeta);
		onChange(newValue, actionMeta);
	};
	setValue = (newValue, action, option) => {
		const { closeMenuOnSelect, isMulti, inputValue } = this.props;
		this.onInputChange('', { action: 'set-value', prevInputValue: inputValue });
		if (closeMenuOnSelect) {
			this.setState({
				inputIsHiddenAfterUpdate: !isMulti,
			});
			this.onMenuClose();
		}
		// when the select value should change, we should reset focusedValue
		this.setState({ clearFocusValueOnUpdate: true });
		this.onChange(newValue, { action, option });
	};
	selectOption = (newValue) => {
		const { blurInputOnSelect, isMulti, name } = this.props;
		const { selectValue } = this.state;
		const deselected = isMulti && this.isOptionSelected(newValue, selectValue);
		const isDisabled = this.isOptionDisabled(newValue, selectValue);

		if (deselected) {
			const candidate = this.getOptionValue(newValue);
			this.setValue(
				multiValueAsValue(selectValue.filter((i) => this.getOptionValue(i) !== candidate)),
				'deselect-option',
				newValue
			);
		} else if (!isDisabled) {
			// Select option if option is not disabled
			if (isMulti) {
				this.setValue(
					multiValueAsValue([...selectValue, newValue]),
					'select-option',
					newValue
				);
			} else {
				this.setValue(singleValueAsValue(newValue), 'select-option');
			}
		} else {
			this.ariaOnChange(singleValueAsValue(newValue), {
				action: 'select-option',
				option: newValue,
				name,
			});
			return;
		}

		if (blurInputOnSelect) {
			this.blurInput();
		}
	};
	removeValue = (removedValue) => {
		const { isMulti } = this.props;
		const { selectValue } = this.state;
		const candidate = this.getOptionValue(removedValue);
		const newValueArray = selectValue.filter((i) => this.getOptionValue(i) !== candidate);
		const newValue = valueTernary(isMulti, newValueArray, newValueArray[0] || null);

		this.onChange(newValue, { action: 'remove-value', removedValue });
		this.focusInput();
	};
	clearValue = () => {
		const { selectValue } = this.state;
		this.onChange(valueTernary(this.props.isMulti, [], null), {
			action: 'clear',
			removedValues: selectValue,
		});
	};
	popValue = () => {
		const { isMulti } = this.props;
		const { selectValue } = this.state;
		const lastSelectedValue = selectValue[selectValue.length - 1];
		const newValueArray = selectValue.slice(0, selectValue.length - 1);
		const newValue = valueTernary(isMulti, newValueArray, newValueArray[0] || null);

		this.onChange(newValue, {
			action: 'pop-value',
			removedValue: lastSelectedValue,
		});
	};

	// ==============================
	// Getters
	// ==============================

	getTheme() {
		// Use the default theme if there are no customisations.
		if (!this.props.theme) {
			return defaultTheme;
		}
		// If the theme prop is a function, assume the function
		// knows how to merge the passed-in default theme with
		// its own modifications.
		if (typeof this.props.theme === 'function') {
			return this.props.theme(defaultTheme);
		}
		// Otherwise, if a plain theme object was passed in,
		// overlay it with the default theme.
		return {
			...defaultTheme,
			...this.props.theme,
		};
	}

	getFocusedOptionId = (focusedOption) => {
		return getFocusedOptionId(this.state.focusableOptionsWithIds, focusedOption);
	};

	getFocusableOptionsWithIds = () => {
		return buildFocusableOptionsWithIds(
			buildCategorizedOptions(this.props, this.state.selectValue),
			this.getElementId('option')
		);
	};

	getValue = () => this.state.selectValue;

	cx = (...args) => classNames(this.props.classNamePrefix, ...args);

	getCommonProps() {
		const {
			clearValue,
			cx,
			getStyles,
			getClassNames,
			getValue,
			selectOption,
			setValue,
			props,
		} = this;
		const { isMulti, isRtl, options } = props;
		const hasValue = this.hasValue();

		return {
			clearValue,
			cx,
			getStyles,
			getClassNames,
			getValue,
			hasValue,
			isMulti,
			isRtl,
			options,
			selectOption,
			selectProps: props,
			setValue,
			theme: this.getTheme(),
		};
	}

	getOptionLabel = (data) => {
		return getOptionLabel(this.props, data);
	};
	getOptionValue = (data) => {
		return getOptionValue(this.props, data);
	};
	getStyles = (key, props) => {
		const { unstyled } = this.props;
		const base = defaultStyles[key](props, unstyled);
		base.boxSizing = 'border-box';
		const custom = this.props.styles[key];
		return custom ? custom(base, props) : base;
	};
	getClassNames = (key, props) => this.props.classNames[key]?.(props);
	getElementId = (element) => {
		return `${this.state.instancePrefix}-${element}`;
	};

	getComponents = () => {
		return defaultComponents(this.props);
	};

	buildCategorizedOptions = () => buildCategorizedOptions(this.props, this.state.selectValue);
	getCategorizedOptions = () => (this.props.menuIsOpen ? this.buildCategorizedOptions() : []);
	buildFocusableOptions = () =>
		buildFocusableOptionsFromCategorizedOptions(this.buildCategorizedOptions());
	getFocusableOptions = () => (this.props.menuIsOpen ? this.buildFocusableOptions() : []);

	// ==============================
	// Helpers
	// ==============================

	ariaOnChange = (value, actionMeta) => {
		this.setState({ ariaSelection: { value, ...actionMeta } });
	};

	hasValue() {
		const { selectValue } = this.state;
		return selectValue.length > 0;
	}
	hasOptions() {
		return !!this.getFocusableOptions().length;
	}
	isClearable() {
		const { isClearable, isMulti } = this.props;

		// single select, by default, IS NOT clearable
		// multi select, by default, IS clearable
		if (isClearable === undefined) return isMulti;

		return isClearable;
	}
	isOptionDisabled(option, selectValue) {
		return isOptionDisabled(this.props, option, selectValue);
	}
	isOptionSelected(option, selectValue) {
		return isOptionSelected(this.props, option, selectValue);
	}
	filterOption(option, inputValue) {
		return filterOption(this.props, option, inputValue);
	}
	formatOptionLabel(data, context) {
		if (typeof this.props.formatOptionLabel === 'function') {
			const { inputValue } = this.props;
			const { selectValue } = this.state;
			return this.props.formatOptionLabel(data, {
				context,
				inputValue,
				selectValue,
			});
		}
		return this.getOptionLabel(data);
	}
	formatGroupLabel(data) {
		return this.props.formatGroupLabel(data);
	}

	// ==============================
	// Mouse Handlers
	// ==============================

	onMenuMouseDown = (event) => {
		if (event.button !== 0) {
			return;
		}
		event.stopPropagation();
		event.preventDefault();
		this.focusInput();
	};
	onMenuMouseMove = (event) => {
		this.blockOptionHover = false;
	};
	onControlMouseDown = (event) => {
		// Event captured by dropdown indicator
		if (event.defaultPrevented) {
			return;
		}
		const { openMenuOnClick } = this.props;
		if (!this.state.isFocused) {
			if (openMenuOnClick) {
				this.openAfterFocus = true;
			}
			this.focusInput();
		} else if (!this.props.menuIsOpen) {
			if (openMenuOnClick) {
				this.openMenu('first');
			}
		} else if (event.target.tagName !== 'INPUT' && event.target.tagName !== 'TEXTAREA') {
			this.onMenuClose();
		}
		if (event.target.tagName !== 'INPUT' && event.target.tagName !== 'TEXTAREA') {
			event.preventDefault();
		}
	};
	onDropdownIndicatorMouseDown = (event) => {
		// ignore mouse events that weren't triggered by the primary button
		if (event && event.type === 'mousedown' && event.button !== 0) {
			return;
		}
		if (this.props.isDisabled) return;
		const { isMulti, menuIsOpen } = this.props;
		this.focusInput();
		if (menuIsOpen) {
			this.setState({ inputIsHiddenAfterUpdate: !isMulti });
			this.onMenuClose();
		} else {
			this.openMenu('first');
		}
		event.preventDefault();
	};
	onClearIndicatorMouseDown = (event) => {
		// ignore mouse events that weren't triggered by the primary button
		if (event && event.type === 'mousedown' && event.button !== 0) {
			return;
		}
		this.clearValue();
		event.preventDefault();
		this.openAfterFocus = false;
		if (event.type === 'touchend') {
			this.focusInput();
		} else {
			setTimeout(() => this.focusInput());
		}
	};
	onScroll = (event) => {
		if (typeof this.props.closeMenuOnScroll === 'boolean') {
			if (event.target instanceof HTMLElement && isDocumentElement(event.target)) {
				this.props.onMenuClose();
			}
		} else if (typeof this.props.closeMenuOnScroll === 'function') {
			if (this.props.closeMenuOnScroll(event)) {
				this.props.onMenuClose();
			}
		}
	};

	// ==============================
	// Composition Handlers
	// ==============================

	startListeningComposition() {
		if (document && document.addEventListener) {
			document.addEventListener('compositionstart', this.onCompositionStart, false);
			document.addEventListener('compositionend', this.onCompositionEnd, false);
		}
	}
	stopListeningComposition() {
		if (document && document.removeEventListener) {
			document.removeEventListener('compositionstart', this.onCompositionStart);
			document.removeEventListener('compositionend', this.onCompositionEnd);
		}
	}
	onCompositionStart = () => {
		this.isComposing = true;
	};
	onCompositionEnd = () => {
		this.isComposing = false;
	};

	// ==============================
	// Touch Handlers
	// ==============================

	startListeningToTouch() {
		if (document && document.addEventListener) {
			document.addEventListener('touchstart', this.onTouchStart, false);
			document.addEventListener('touchmove', this.onTouchMove, false);
			document.addEventListener('touchend', this.onTouchEnd, false);
		}
	}
	stopListeningToTouch() {
		if (document && document.removeEventListener) {
			document.removeEventListener('touchstart', this.onTouchStart);
			document.removeEventListener('touchmove', this.onTouchMove);
			document.removeEventListener('touchend', this.onTouchEnd);
		}
	}
	onTouchStart = ({ touches }) => {
		const touch = touches && touches.item(0);
		if (!touch) {
			return;
		}

		this.initialTouchX = touch.clientX;
		this.initialTouchY = touch.clientY;
		this.userIsDragging = false;
	};
	onTouchMove = ({ touches }) => {
		const touch = touches && touches.item(0);
		if (!touch) {
			return;
		}

		const deltaX = Math.abs(touch.clientX - this.initialTouchX);
		const deltaY = Math.abs(touch.clientY - this.initialTouchY);
		const moveThreshold = 5;

		this.userIsDragging = deltaX > moveThreshold || deltaY > moveThreshold;
	};
	onTouchEnd = (event) => {
		if (this.userIsDragging) return;

		// close the menu if the user taps outside
		// we're checking on event.target here instead of event.currentTarget, because we want to assert information
		// on events on child elements, not the document (which we've attached this handler to).
		if (
			this.controlRef &&
			!this.controlRef.contains(event.target) &&
			this.menuListRef &&
			!this.menuListRef.contains(event.target)
		) {
			this.blurInput();
		}

		// reset move vars
		this.initialTouchX = 0;
		this.initialTouchY = 0;
	};
	onControlTouchEnd = (event) => {
		if (this.userIsDragging) return;
		this.onControlMouseDown(event);
	};
	onClearIndicatorTouchEnd = (event) => {
		if (this.userIsDragging) return;

		this.onClearIndicatorMouseDown(event);
	};
	onDropdownIndicatorTouchEnd = (event) => {
		if (this.userIsDragging) return;

		this.onDropdownIndicatorMouseDown(event);
	};

	// ==============================
	// Focus Handlers
	// ==============================

	handleInputChange = (event) => {
		const { inputValue: prevInputValue } = this.props;
		const inputValue = event.currentTarget.value;
		this.setState({ inputIsHiddenAfterUpdate: false });
		this.onInputChange(inputValue, { action: 'input-change', prevInputValue });
		if (!this.props.menuIsOpen) {
			this.onMenuOpen();
		}
	};
	onInputFocus = (event) => {
		if (this.props.onFocus) {
			this.props.onFocus(event);
		}
		this.setState({
			inputIsHiddenAfterUpdate: false,
			isFocused: true,
		});
		if (this.openAfterFocus || this.props.openMenuOnFocus) {
			this.openMenu('first');
		}
		this.openAfterFocus = false;
	};
	onInputBlur = (event) => {
		const { inputValue: prevInputValue } = this.props;
		if (this.menuListRef && this.menuListRef.contains(document.activeElement)) {
			this.inputRef.focus();
			return;
		}
		if (this.props.onBlur) {
			this.props.onBlur(event);
		}
		this.onInputChange('', { action: 'input-blur', prevInputValue });
		this.onMenuClose();
		this.setState({
			focusedValue: null,
			isFocused: false,
		});
	};
	onOptionHover = (focusedOption) => {
		if (this.blockOptionHover || this.state.focusedOption === focusedOption) {
			return;
		}
		const options = this.getFocusableOptions();
		const focusedOptionIndex = options.indexOf(focusedOption);
		this.setState({
			focusedOption,
			focusedOptionId:
				focusedOptionIndex > -1 ? this.getFocusedOptionId(focusedOption) : null,
		});
	};
	shouldHideSelectedOptions = () => {
		return shouldHideSelectedOptions(this.props);
	};

	// If the hidden input gets focus through form submit,
	// redirect focus to focusable input.
	onValueInputFocus = (e) => {
		e.preventDefault();
		e.stopPropagation();

		this.focus();
	};

	// ==============================
	// Keyboard Handlers
	// ==============================

	onKeyDown = (event) => {
		const {
			isMulti,
			backspaceRemovesValue,
			escapeClearsValue,
			inputValue,
			isClearable,
			isDisabled,
			menuIsOpen,
			onKeyDown,
			tabSelectsValue,
			openMenuOnFocus,
		} = this.props;
		const { focusedOption, focusedValue, selectValue } = this.state;

		if (isDisabled) return;

		if (typeof onKeyDown === 'function') {
			onKeyDown(event);
			if (event.defaultPrevented) {
				return;
			}
		}

		// Block option hover events when the user has just pressed a key
		this.blockOptionHover = true;
		switch (event.key) {
			case 'ArrowLeft':
				if (!isMulti || inputValue) return;
				this.focusValue('previous');
				break;
			case 'ArrowRight':
				if (!isMulti || inputValue) return;
				this.focusValue('next');
				break;
			case 'Delete':
			case 'Backspace':
				if (inputValue) return;
				if (focusedValue) {
					this.removeValue(focusedValue);
				} else {
					if (!backspaceRemovesValue) return;
					if (isMulti) {
						this.popValue();
					} else if (isClearable) {
						this.clearValue();
					}
				}
				break;
			case 'Tab':
				if (this.isComposing) return;

				if (
					event.shiftKey ||
					!menuIsOpen ||
					!tabSelectsValue ||
					!focusedOption ||
					// don't capture the event if the menu opens on focus and the focused
					// option is already selected; it breaks the flow of navigation
					(openMenuOnFocus && this.isOptionSelected(focusedOption, selectValue))
				) {
					return;
				}
				this.selectOption(focusedOption);
				break;
			case 'Enter':
				if (event.keyCode === 229) {
					// ignore the keydown event from an Input Method Editor(IME)
					// ref. https://www.w3.org/TR/uievents/#determine-keydown-keyup-keyCode
					break;
				}
				if (menuIsOpen) {
					if (!focusedOption) return;
					if (this.isComposing) return;
					this.selectOption(focusedOption);
					break;
				}
				return;
			case 'Escape':
				if (menuIsOpen) {
					this.setState({
						inputIsHiddenAfterUpdate: false,
					});
					this.onInputChange('', {
						action: 'menu-close',
						prevInputValue: inputValue,
					});
					this.onMenuClose();
				} else if (isClearable && escapeClearsValue) {
					this.clearValue();
				}
				break;
			case ' ': // space
				if (inputValue) {
					return;
				}
				if (!menuIsOpen) {
					this.openMenu('first');
					break;
				}
				if (!focusedOption) return;
				this.selectOption(focusedOption);
				break;
			case 'ArrowUp':
				if (menuIsOpen) {
					this.focusOption('up');
				} else {
					this.openMenu('last');
				}
				break;
			case 'ArrowDown':
				if (menuIsOpen) {
					this.focusOption('down');
				} else {
					this.openMenu('first');
				}
				break;
			case 'PageUp':
				if (!menuIsOpen) return;
				this.focusOption('pageup');
				break;
			case 'PageDown':
				if (!menuIsOpen) return;
				this.focusOption('pagedown');
				break;
			case 'Home':
				if (!menuIsOpen) return;
				this.focusOption('first');
				break;
			case 'End':
				if (!menuIsOpen) return;
				this.focusOption('last');
				break;
			default:
				return;
		}
		event.preventDefault();
	};

	// ==============================
	// Renderers
	// ==============================
	renderInput() {
		const {
			isDisabled,
			isSearchable,
			inputId,
			inputValue,
			tabIndex,
			form,
			menuIsOpen,
			required,
		} = this.props;
		const { Input } = this.getComponents();
		const { inputIsHidden, ariaSelection } = this.state;
		const { commonProps } = this;

		const id = inputId || this.getElementId('input');

		// aria attributes makes the JSX "noisy", separated for clarity
		const ariaAttributes = {
			'aria-autocomplete': 'list',
			'aria-expanded': menuIsOpen,
			'aria-haspopup': true,
			'aria-errormessage': this.props['aria-errormessage'],
			'aria-invalid': this.props['aria-invalid'],
			'aria-label': this.props['aria-label'],
			'aria-labelledby': this.props['aria-labelledby'],
			'aria-required': required,
			role: 'combobox',
			'aria-activedescendant': this.isAppleDevice
				? undefined
				: this.state.focusedOptionId || '',

			...(menuIsOpen && {
				'aria-controls': this.getElementId('listbox'),
			}),
			...(!isSearchable && {
				'aria-readonly': true,
			}),
			...(this.hasValue()
				? ariaSelection?.action === 'initial-input-focus' && {
						'aria-describedby': this.getElementId('live-region'),
				  }
				: {
						'aria-describedby': this.getElementId('placeholder'),
				  }),
		};

		if (!isSearchable) {
			// use a dummy input to maintain focus/blur functionality
			return (
				<DummyInput
					id={id}
					innerRef={this.getInputRef}
					onBlur={this.onInputBlur}
					onChange={noop}
					onFocus={this.onInputFocus}
					disabled={isDisabled}
					tabIndex={tabIndex}
					inputMode="none"
					form={form}
					value=""
					{...ariaAttributes}
				/>
			);
		}

		return (
			<Input
				{...commonProps}
				autoCapitalize="none"
				autoComplete="off"
				autoCorrect="off"
				id={id}
				innerRef={this.getInputRef}
				isDisabled={isDisabled}
				isHidden={inputIsHidden}
				onBlur={this.onInputBlur}
				onChange={this.handleInputChange}
				onFocus={this.onInputFocus}
				spellCheck="false"
				tabIndex={tabIndex}
				form={form}
				type="text"
				value={inputValue}
				{...ariaAttributes}
			/>
		);
	}
	renderPlaceholderOrValue() {
		const {
			MultiValue,
			MultiValueContainer,
			MultiValueLabel,
			MultiValueRemove,
			SingleValue,
			Placeholder,
		} = this.getComponents();
		const { commonProps } = this;
		const { controlShouldRenderValue, isDisabled, isMulti, inputValue, placeholder } =
			this.props;
		const { selectValue, focusedValue, isFocused } = this.state;

		if (!this.hasValue() || !controlShouldRenderValue) {
			return inputValue ? null : (
				<Placeholder
					{...commonProps}
					key="placeholder"
					isDisabled={isDisabled}
					isFocused={isFocused}
					innerProps={{ id: this.getElementId('placeholder') }}
				>
					{placeholder}
				</Placeholder>
			);
		}

		if (isMulti) {
			return selectValue.map((opt, index) => {
				const isOptionFocused = opt === focusedValue;
				const key = `${this.getOptionLabel(opt)}-${this.getOptionValue(opt)}`;

				return (
					<MultiValue
						{...commonProps}
						components={{
							Container: MultiValueContainer,
							Label: MultiValueLabel,
							Remove: MultiValueRemove,
						}}
						isFocused={isOptionFocused}
						isDisabled={isDisabled}
						key={key}
						index={index}
						removeProps={{
							onClick: () => this.removeValue(opt),
							onTouchEnd: () => this.removeValue(opt),
							onMouseDown: (e) => {
								e.preventDefault();
							},
						}}
						data={opt}
					>
						{this.formatOptionLabel(opt, 'value')}
					</MultiValue>
				);
			});
		}

		if (inputValue) {
			return null;
		}

		const singleValue = selectValue[0];
		return (
			<SingleValue {...commonProps} data={singleValue} isDisabled={isDisabled}>
				{this.formatOptionLabel(singleValue, 'value')}
			</SingleValue>
		);
	}
	renderClearIndicator() {
		const { ClearIndicator } = this.getComponents();
		const { commonProps } = this;
		const { isDisabled, isLoading } = this.props;
		const { isFocused } = this.state;

		if (!this.isClearable() || !ClearIndicator || isDisabled || !this.hasValue() || isLoading) {
			return null;
		}

		const innerProps = {
			onMouseDown: this.onClearIndicatorMouseDown,
			onTouchEnd: this.onClearIndicatorTouchEnd,
			'aria-hidden': 'true',
		};

		return <ClearIndicator {...commonProps} innerProps={innerProps} isFocused={isFocused} />;
	}
	renderLoadingIndicator() {
		const { LoadingIndicator } = this.getComponents();
		const { commonProps } = this;
		const { isDisabled, isLoading } = this.props;
		const { isFocused } = this.state;

		if (!LoadingIndicator || !isLoading) return null;

		const innerProps = { 'aria-hidden': 'true' };
		return (
			<LoadingIndicator
				{...commonProps}
				innerProps={innerProps}
				isDisabled={isDisabled}
				isFocused={isFocused}
			/>
		);
	}
	renderIndicatorSeparator() {
		const { DropdownIndicator, IndicatorSeparator } = this.getComponents();

		// separator doesn't make sense without the dropdown indicator
		if (!DropdownIndicator || !IndicatorSeparator) return null;

		const { commonProps } = this;
		const { isDisabled } = this.props;
		const { isFocused } = this.state;

		return (
			<IndicatorSeparator {...commonProps} isDisabled={isDisabled} isFocused={isFocused} />
		);
	}
	renderDropdownIndicator() {
		const { DropdownIndicator } = this.getComponents();
		if (!DropdownIndicator) return null;
		const { commonProps } = this;
		const { isDisabled } = this.props;
		const { isFocused } = this.state;

		const innerProps = {
			onMouseDown: this.onDropdownIndicatorMouseDown,
			onTouchEnd: this.onDropdownIndicatorTouchEnd,
			'aria-hidden': 'true',
		};

		return (
			<DropdownIndicator
				{...commonProps}
				innerProps={innerProps}
				isDisabled={isDisabled}
				isFocused={isFocused}
			/>
		);
	}
	renderMenu() {
		const {
			Group,
			GroupHeading,
			Menu,
			MenuList,
			MenuPortal,
			LoadingMessage,
			NoOptionsMessage,
			Option,
		} = this.getComponents();
		const { commonProps } = this;
		const { focusedOption } = this.state;
		const {
			captureMenuScroll,
			inputValue,
			isLoading,
			loadingMessage,
			minMenuHeight,
			maxMenuHeight,
			menuIsOpen,
			menuPlacement,
			menuPosition,
			menuPortalTarget,
			menuShouldBlockScroll,
			menuShouldScrollIntoView,
			noOptionsMessage,
			onMenuScrollToTop,
			onMenuScrollToBottom,
		} = this.props;

		if (!menuIsOpen) return null;

		// TODO: Internal Option Type here
		const render = (props, id) => {
			const { type, data, isDisabled, isSelected, label, value } = props;
			const isFocused = focusedOption === data;
			const onHover = isDisabled ? undefined : () => this.onOptionHover(data);
			const onSelect = isDisabled ? undefined : () => this.selectOption(data);
			const optionId = `${this.getElementId('option')}-${id}`;
			const innerProps = {
				id: optionId,
				onClick: onSelect,
				onMouseMove: onHover,
				onMouseOver: onHover,
				tabIndex: -1,
				role: 'option',
				'aria-selected': this.isAppleDevice ? undefined : isSelected, // is not supported on Apple devices
			};

			return (
				<Option
					{...commonProps}
					innerProps={innerProps}
					data={data}
					isDisabled={isDisabled}
					isSelected={isSelected}
					key={optionId}
					label={label}
					type={type}
					value={value}
					isFocused={isFocused}
					innerRef={isFocused ? this.getFocusedOptionRef : undefined}
				>
					{this.formatOptionLabel(props.data, 'menu')}
				</Option>
			);
		};

		let menuUI;

		if (this.hasOptions()) {
			menuUI = this.getCategorizedOptions().map((item) => {
				if (item.type === 'group') {
					const { data, options, index: groupIndex } = item;
					const groupId = `${this.getElementId('group')}-${groupIndex}`;
					const headingId = `${groupId}-heading`;

					return (
						<Group
							{...commonProps}
							key={groupId}
							data={data}
							options={options}
							Heading={GroupHeading}
							headingProps={{
								id: headingId,
								data: item.data,
							}}
							label={this.formatGroupLabel(item.data)}
						>
							{item.options.map((option) =>
								render(option, `${groupIndex}-${option.index}`)
							)}
						</Group>
					);
				} else if (item.type === 'option') {
					return render(item, `${item.index}`);
				}
			});
		} else if (isLoading) {
			const message = loadingMessage({ inputValue });
			if (message === null) return null;
			menuUI = <LoadingMessage {...commonProps}>{message}</LoadingMessage>;
		} else {
			const message = noOptionsMessage({ inputValue });
			if (message === null) return null;
			menuUI = <NoOptionsMessage {...commonProps}>{message}</NoOptionsMessage>;
		}
		const menuPlacementProps = {
			minMenuHeight,
			maxMenuHeight,
			menuPlacement,
			menuPosition,
			menuShouldScrollIntoView,
		};

		const menuElement = (
			<MenuPlacer {...commonProps} {...menuPlacementProps}>
				{({ ref, placerProps: { placement, maxHeight } }) => (
					<Menu
						{...commonProps}
						{...menuPlacementProps}
						innerRef={ref}
						innerProps={{
							onMouseDown: this.onMenuMouseDown,
							onMouseMove: this.onMenuMouseMove,
						}}
						isLoading={isLoading}
						placement={placement}
					>
						<ScrollManager
							captureEnabled={captureMenuScroll}
							onTopArrive={onMenuScrollToTop}
							onBottomArrive={onMenuScrollToBottom}
							lockEnabled={menuShouldBlockScroll}
						>
							{(scrollTargetRef) => (
								<MenuList
									{...commonProps}
									innerRef={(instance) => {
										this.getMenuListRef(instance);
										scrollTargetRef(instance);
									}}
									innerProps={{
										role: 'listbox',
										'aria-multiselectable': commonProps.isMulti,
										id: this.getElementId('listbox'),
									}}
									isLoading={isLoading}
									maxHeight={maxHeight}
									focusedOption={focusedOption}
								>
									{menuUI}
								</MenuList>
							)}
						</ScrollManager>
					</Menu>
				)}
			</MenuPlacer>
		);

		// positioning behaviour is almost identical for portalled and fixed,
		// so we use the same component. the actual portalling logic is forked
		// within the component based on `menuPosition`
		return menuPortalTarget || menuPosition === 'fixed' ? (
			<MenuPortal
				{...commonProps}
				appendTo={menuPortalTarget}
				controlElement={this.controlRef}
				menuPlacement={menuPlacement}
				menuPosition={menuPosition}
			>
				{menuElement}
			</MenuPortal>
		) : (
			menuElement
		);
	}
	renderFormField() {
		const { delimiter, isDisabled, isMulti, name, required } = this.props;
		const { selectValue } = this.state;

		if (required && !this.hasValue() && !isDisabled) {
			return <RequiredInput name={name} onFocus={this.onValueInputFocus} />;
		}

		if (!name || isDisabled) return;

		if (isMulti) {
			if (delimiter) {
				const value = selectValue.map((opt) => this.getOptionValue(opt)).join(delimiter);
				return <input name={name} type="hidden" value={value} />;
			}
			const input =
				selectValue.length > 0 ? (
					selectValue.map((opt, i) => (
						<input
							key={`i-${i}`}
							name={name}
							type="hidden"
							value={this.getOptionValue(opt)}
						/>
					))
				) : (
					<input name={name} type="hidden" value="" />
				);

			return <div>{input}</div>;
		}
		const value = selectValue[0] ? this.getOptionValue(selectValue[0]) : '';
		return <input name={name} type="hidden" value={value} />;
	}

	renderLiveRegion() {
		const { commonProps } = this;
		const { ariaSelection, focusedOption, focusedValue, isFocused, selectValue } = this.state;

		const focusableOptions = this.getFocusableOptions();

		return (
			<LiveRegion
				{...commonProps}
				id={this.getElementId('live-region')}
				ariaSelection={ariaSelection}
				focusedOption={focusedOption}
				focusedValue={focusedValue}
				isFocused={isFocused}
				selectValue={selectValue}
				focusableOptions={focusableOptions}
				isAppleDevice={this.isAppleDevice}
			/>
		);
	}

	render() {
		const { Control, IndicatorsContainer, SelectContainer, ValueContainer } =
			this.getComponents();

		const { className, id, isDisabled, menuIsOpen } = this.props;
		const { isFocused } = this.state;
		const commonProps = (this.commonProps = this.getCommonProps());

		return (
			<SelectContainer
				{...commonProps}
				className={className}
				innerProps={{
					id,
					onKeyDown: this.onKeyDown,
				}}
				isDisabled={isDisabled}
				isFocused={isFocused}
			>
				{this.renderLiveRegion()}
				<Control
					{...commonProps}
					innerRef={this.getControlRef}
					innerProps={{
						onMouseDown: this.onControlMouseDown,
						onTouchEnd: this.onControlTouchEnd,
					}}
					isDisabled={isDisabled}
					isFocused={isFocused}
					menuIsOpen={menuIsOpen}
				>
					<ValueContainer {...commonProps} isDisabled={isDisabled}>
						{this.renderPlaceholderOrValue()}
						{this.renderInput()}
					</ValueContainer>
					<IndicatorsContainer {...commonProps} isDisabled={isDisabled}>
						{this.renderClearIndicator()}
						{this.renderLoadingIndicator()}
						{this.renderIndicatorSeparator()}
						{this.renderDropdownIndicator()}
					</IndicatorsContainer>
				</Control>
				{this.renderMenu()}
				{this.renderFormField()}
			</SelectContainer>
		);
	}
}
