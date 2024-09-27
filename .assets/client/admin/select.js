import React, {useState, useEffect} from 'react';
import PropTypes from 'prop-types';
import Downshift from 'downshift';

const Select = ({
					className,
					id,
					label,
					help,
					variant,
					getOptionLabel,
					getOptionValue,
					isDisabled,
					isLoading,
					isMulti,
					isSearchable,
					isClearable,
					loadOptions,
					options,
					isOptionDisabled,
					loadingMessage,
					noOptionsMessage,
					createMessage,
					placeholder,
					maxSelections,
					onChange,
					onCreate,
					onInputChange,
					required,
				}) => {

	const [inputValue, setInputValue] = useState('');
	const [filteredOptions, setFilteredOptions] = useState(options);

	useEffect(() => {
		if (loadOptions) {
			loadOptions(inputValue).then(setFilteredOptions);
		} else {
			setFilteredOptions(options.filter(option =>
				getOptionLabel(option).toLowerCase().includes(inputValue.toLowerCase())
			));
		}
	}, [inputValue, loadOptions, options, getOptionLabel]);

	const handleInputChange = (value) => {
		setInputValue(value);
		if (onInputChange) {
			onInputChange(value);
		}
	};

	const handleChange = (selectedItems) => {
		if (onChange) {
			onChange(selectedItems);
		}
	};

	return (
		<div className={className}>
			{label && <label htmlFor={id}>{label}{required && '*'}</label>}
			{help && <small>{help}</small>}

			<Downshift
				onChange={handleChange}
				itemToString={item => (item ? getOptionLabel(item) : '')}
				isDisabled={isDisabled}
				inputValue={inputValue}
				onInputValueChange={handleInputChange}
				selectedItem={isMulti ? [] : null}
			>
				{({
					  getInputProps,
					  getLabelProps,
					  getToggleButtonProps,
					  getMenuProps,
					  getItemProps,
					  isOpen,
					  inputValue: downshiftInputValue,
					  highlightedIndex
				  }) => (
					<div>
						<input
							{...getInputProps({
								id: id,
								placeholder: placeholder,
								disabled: isDisabled,
								required: required,
								onFocus: () => setInputValue(''),
							})}
						/>
						<ul {...getMenuProps()} style={{display: isOpen ? 'block' : 'none'}}>
							{isLoading && <li>{loadingMessage()}</li>}
							{!isLoading && filteredOptions.length === 0 && <li>{noOptionsMessage()}</li>}
							{filteredOptions.map((option, index) => (
								<li
									{...getItemProps({
										key: getOptionValue(option),
										index,
										item: option,
										style: {
											backgroundColor: highlightedIndex === index ? '#bde4ff' : '#fff',
											cursor: 'pointer'
										},
									})}
									disabled={isOptionDisabled(option)}
								>
									{getOptionLabel(option)}
								</li>
							))}
						</ul>
					</div>
				)}
			</Downshift>
		</div>
	);
};

Select.propTypes = {
	className: PropTypes.string,
	id: PropTypes.string,
	label: PropTypes.string,
	help: PropTypes.string,
	variant: PropTypes.oneOf(['normal', 'inline']),
	getOptionLabel: PropTypes.func,
	getOptionValue: PropTypes.func,
	isDisabled: PropTypes.bool,
	isLoading: PropTypes.bool,
	isMulti: PropTypes.bool,
	isSearchable: PropTypes.bool,
	isClearable: PropTypes.bool,
	loadOptions: PropTypes.func,
	options: PropTypes.array,
	isOptionDisabled: PropTypes.func,
	loadingMessage: PropTypes.func,
	noOptionsMessage: PropTypes.func,
	createMessage: PropTypes.func,
	placeholder: PropTypes.string,
	maxSelections: PropTypes.number,
	onChange: PropTypes.func,
	onCreate: PropTypes.func,
	onInputChange: PropTypes.func,
	required: PropTypes.bool,
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
	loadingMessage: () => 'Loading…',
	noOptionsMessage: () => 'No options',
	createMessage: (inputVal) => `Create "${inputVal}"`,
	placeholder: 'Select…',
	maxSelections: null,
	onChange: null,
	onCreate: () => {
	},
	onInputChange: null,
	required: false,
};

export default Select;
