/**
 * External dependencies
 */
import { debounce, isEmpty, isArray } from 'lodash';
import classnames from 'classnames';

/**
 * WordPress dependencies
 */
import {
	forwardRef,
	useCallback,
	useEffect,
	useRef,
	useState,
	Component,
} from '@wordpress/element';
import { useMergeRefs, useInstanceId } from '@wordpress/compose';
import { withFocusOutside, BaseControl } from '@wordpress/components';
import { chevronDown, Icon, search } from '@wordpress/icons';
import { ESCAPE, TAB, ENTER } from '@wordpress/keycodes';
import { __, spintf } from '@wordpress/i18n';

/**
 * Internal dependencies
 */
import './style.scss';
// import { withFocusOutside } from '../../hocs';
import { useControlledValue } from '../../hooks';
import { useAsync, normalizeValue } from './utils';

const DetectOutside = withFocusOutside(
	class extends Component {
		handleFocusOutside(event) {
			this.props.onFocusOutside(event);
		}

		render() {
			return this.props.children;
		}
	}
);

const Select = forwardRef((selectProps, ref) => {
	const props = useAsync(selectProps);
	const {
		className,
		id: propsId,
		label,
		help,
		defaultValue,
		value: propsValue,
		onChange: propsOnChange,
		IsMenuOpen: propsIsMenuOpen,
		onMenuOpen: propsOnMenuOpen,
		onMenuClose: propsOnMenuClose,
		isMulti = false,
		isDisabled,
		isSearchable = true,
		maxSelections,
		onCreate,
		hideLabelFromVision,
		isLoading,
	} = props;
	const instanceId = useInstanceId(Select);
	const id = `eac-select-control-${instanceId}` || propsId;
	// ==============================
	// States
	// ==============================
	const [value, setValue] = useControlledValue({
		value: propsValue,
		onChange: propsOnChange,
		defaultValue,
	});
	const [isMenuOpen, setIsMenuOpen] = useControlledValue({
		value: propsIsMenuOpen,
		onChange: value ? propsOnMenuOpen : propsOnMenuClose,
		defaultValue: false,
	});
	const [focusedOption, setFocusedOption] = useState(null);
	const [focusedValue, setFocusedValue] = useState(null);
	const [isCreating, setIsCreating] = useState(false);
	// ==============================
	// Refs
	// ==============================
	const selectRef = useRef();
	const valueContainerRef = useRef();
	const inputRef = useRef();
	const optionsRef = useRef();
	const focusedOptionRef = useRef();
	const focusedValueRef = useRef();
	const mergeRefs = useMergeRefs([ref, selectRef]);
	// ==============================
	// Methods
	// ==============================
	const isSingleValue = !isMulti || (maxSelections && maxSelections === 1);
	const isCreatable = typeof onCreate === 'function';
	const hasValue = !isEmpty(value);
	const isInputFocused = selectRef?.current === selectRef?.current?.ownerDocument.activeElement;
	const onFocusOutside = useCallback(() => {
		console.log('onFocusOutside');
		if (isMenuOpen) {
			setIsMenuOpen(false);
		}
	}, [isMenuOpen, setIsMenuOpen]);
	const onKeyDown = (event) => {
		if (isDisabled) {
			return;
		}
		if (ENTER === event.keyCode) {
			event.preventDefault();
		}
		if (event.keyCode !== ESCAPE && event.keyCode !== TAB && !event.shiftKey) {
		}
		openMenu();
	};
	const openMenu = () => {
		// const options = getFocusAbleOptions();
		// let openAtIndex = 0;
		// if (isMulti && value && value.length) {
		// 	const selectedIndex = options.findIndex(value[0]);
		// 	if (selectedIndex > -1) {
		// 		openAtIndex = selectedIndex;
		// 	}
		// }
		// setFocusedValue(null);
		// setFocusedOption(options[openAtIndex]);
		setIsMenuOpen(true);
	};
	const closeMenu = () => {
		setIsMenuOpen(false);
	};

	// ==============================
	// Renderers
	// ==============================
	const renderValueContainer = () => {
		// const containerProps = {
		// 	className: 'eac-select-control__value-container',
		// 	ref: valueContainerRef,
		// 	tabIndex: 0,
		// 	onClick: focusInput,
		// 	onKeyDown: onKeyDown,
		// }

		return (
			<div className="eac-select-control__value-container" ref={valueContainerRef}>
				{value && isMulti && renderMultiValue()}
				{value && !isMulti && renderSingleValue()}
				{!value && renderPlaceholder()}
				{!hasValue && (
					<span className="eac-select-control__indicator">
						<Icon icon={chevronDown} size={18} />
					</span>
				)}
			</div>
		);
	};
	const renderPlaceholder = () => {
		return <span className="eac-select-control__placeholder">Placeholder</span>;
	};
	const renderSingleValue = () => {
		return <div className="eac-select-control__single-value">Single Value</div>;
	};
	const renderMultiValue = () => {
		return <div className="eac-select-control__multi-value">Multi Value</div>;
	};
	const renderDropdown = () => {
		return (
			<div className="eac-select-control__dropdown">
				{renderSearchInput()}
				{renderOptions()}
			</div>
		);
	};
	const renderSearchInput = () => {
		return <div className="eac-select-control__search">Hey</div>;
	};
	const renderOptions = () => {
		return <div className="eac-select-control__options">Options</div>;
	};
	const renderFormField = () => {
		return <div className="eac-select-control__form-field">Form Field</div>;
	};

	const classes = classnames('eac-select-control', className, {
		'eac-select-control--disabled': isDisabled,
		'eac-select-control--loading': isLoading,
		'eac-select-control--multi': isMulti,
		'eac-select-control--single': !isMulti,
		'eac-select-control--searchable': isSearchable,
		'eac-select-control--open': isMenuOpen,
		'eac-select-control--empty': !value || value.length === 0,
	});

	return (
		<DetectOutside onFocusOutside={onFocusOutside}>
			<BaseControl
				className={classes}
				id={`eac-select-control-${instanceId}`}
				label={label}
				hideLabelFromVision={hideLabelFromVision}
				help={help}
			>
				<div
					className="eac-select-control__control"
					tabIndex={0}
					aria-expanded={isMenuOpen}
					aria-haspopup="listbox"
					onKeyDown={onKeyDown}
					ref={mergeRefs}
				>
					{renderValueContainer()}
					{renderDropdown()}
					{renderFormField()}
				</div>
			</BaseControl>
		</DetectOutside>
	);
});

export default Select;
