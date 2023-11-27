/**
 * External dependencies
 */
import classnames from 'classnames';

/**
 * WordPress dependencies
 */
import { useState, useRef, forwardRef } from '@wordpress/element';
import { useInstanceId, useMergeRefs } from '@wordpress/compose';
import { BaseControl, FlexItem } from '@wordpress/components';
import { TAB, ESCAPE, ENTER } from '@wordpress/keycodes';

/**
 * Internal dependencies
 */
import { StyledSelect, ValueContainer, Placeholder } from './styles';
import Dropdown from './dropdown';
import Tag from './tag';

function UnforwardedSelect(props, ref) {
	const {
		className,
		variant = 'normal',
		dropdownWidth,
		name,
		value: propsValue,
		defaultValue,
		placeholder,
		invalid,
		options,
		onChange,
		onCreate,
		isMulti,
		withClearValue,
		renderValue: propsRenderValue,
		renderOption: propsRenderOption,
	} = props;
	const [isFocused, setIsFocused] = useState(false);
	const [isDropdownOpen, setDropdownOpen] = useState(true);
	const [searchValue, setSearchValue] = useState('');
	const [stateValue, setStateValue] = useState(defaultValue || (isMulti ? [] : null));

	const isControlled = propsValue !== undefined;
	const value = isControlled ? propsValue : stateValue;

	const selectRef = useRef();
	const inputRef = useRef();

	const getOption = (optionValue) => options.find((option) => option.value === optionValue);
	const getOptionLabel = (optionValue) => (getOption(optionValue) || { label: '' }).label;
	const isValueEmpty = isMulti ? !value.length : !getOption(value);

	const handleOnFocus = (event) => {
		props?.onFocus?.(event);
		setIsFocused(true);
	};
	const handleOnBlur = (event) => {
		props?.onBlur?.(event);
		setIsFocused(false);
	};
	const activateDropdown = () => {
		if (isDropdownOpen) {
			// $inputRef.current.focus();
		} else {
			setDropdownOpen(true);
		}
	};
	const deactivateDropdown = () => {
		setDropdownOpen(false);
		setSearchValue('');
		selectRef.current.focus();
	};
	const preserveValueType = (newValue) => {
		const areOptionValuesNumbers = options.some((option) => typeof option.value === 'number');

		if (areOptionValuesNumbers) {
			if (isMulti) {
				return newValue.map(Number);
			}
			if (newValue) {
				return Number(newValue);
			}
		}
		return newValue;
	};
	const handleChange = (newValue) => {
		if (!isControlled) {
			setStateValue(preserveValueType(newValue));
		}
		onChange(preserveValueType(newValue));
	};
	const removeOptionValue = (optionValue) => {
		handleChange(value.filter((val) => val !== optionValue));
	};

	const classes = classnames('eac-select-control', className, {
		[`eac-select-control--${variant}`]: variant,
	});

	return (
		<StyledSelect
			className={classes}
			variant={variant}
			onFocus={handleOnFocus}
			onBlur={handleOnBlur}
			tabIndex="0"
			ref={useMergeRefs([ref, selectRef])}
		>
			<ValueContainer
				justify="flex-start"
				align="center"
				gap={1}
				wrap={true}
				variant={variant}
				onClick={activateDropdown}
				isMulti={isMulti}
			>
				{isValueEmpty && <Placeholder>{placeholder}</Placeholder>}

				{!isValueEmpty && !isMulti && propsRenderValue
					? propsRenderValue({ value })
					: getOptionLabel(value)}
				{value.map((optionValue, index) => {
					if (propsRenderValue) {
						return propsRenderValue({
							index,
							value: optionValue,
							removeOptionValue: () => removeOptionValue(optionValue),
						});
					}
					return (
						<FlexItem key={index}>
							<Tag
								label={getOptionLabel(optionValue)}
								onRemove={() => removeOptionValue(optionValue)}
							/>
						</FlexItem>
					);
				})}
			</ValueContainer>
			{isDropdownOpen && (
				<Dropdown
					dropdownWidth={dropdownWidth}
					value={value}
					isValueEmpty={isValueEmpty}
					searchValue={searchValue}
					setSearchValue={setSearchValue}
					selectRef={selectRef}
					inputRef={inputRef}
					deactivateDropdown={deactivateDropdown}
					options={options}
					onChange={handleChange}
					onCreate={onCreate}
					isMulti={isMulti}
					withClearValue={withClearValue}
					propsRenderOption={propsRenderOption}
				/>
			)}
		</StyledSelect>
	);
}

const Select = forwardRef(UnforwardedSelect);

export default Select;
