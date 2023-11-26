/**
 * External dependencies
 */
/**
 * WordPress dependencies
 */
import { useCallback, useState } from '@wordpress/element';
import { normalizeValue } from './utils';

export default function useStateManager({
	defaultInputValue = '',
	defaultMenuIsOpen = false,
	defaultValue = null,
	isMulti = false,
	value: propsValue,
	inputValue: propsInputValue,
	menuIsOpen: propsMenuIsOpen,
	onInputChange: propsOnInputChange,
	onChange: propsOnChange,
	onMenuClose: propsOnMenuClose,
	onMenuOpen: propsOnMenuOpen,
	...restSelectProps
}) {
	const [stateInputValue, setStateInputValue] = useState(
		propsInputValue !== undefined ? propsInputValue : defaultInputValue
	);
	const [stateMenuIsOpen, setStateMenuIsOpen] = useState(
		propsMenuIsOpen !== undefined ? propsMenuIsOpen : defaultMenuIsOpen
	);
	const [stateValue, setStateValue] = useState(
		propsValue !== undefined ? propsValue : defaultValue
	);

	const onChange = useCallback(
		(value, actionMeta) => {
			if (typeof propsOnChange === 'function') {
				propsOnChange(value, actionMeta);
			}
			value = normalizeValue(value, isMulti);
			if (isMulti) {
				setStateValue(value.length ? value : []);
			} else {
				setStateValue(value);
			}
		},
		[isMulti, propsOnChange]
	);
	const onInputChange = useCallback(
		(value, actionMeta) => {
			let newValue;
			if (typeof propsOnInputChange === 'function') {
				newValue = propsOnInputChange(value, actionMeta);
			}
			setStateInputValue(newValue !== undefined ? newValue : value);
		},
		[propsOnInputChange]
	);
	const onMenuOpen = useCallback(() => {
		if (typeof propsOnMenuOpen === 'function') {
			propsOnMenuOpen();
		}
		setStateMenuIsOpen(true);
	}, [propsOnMenuOpen]);
	const onMenuClose = useCallback(() => {
		if (typeof propsOnMenuClose === 'function') {
			propsOnMenuClose();
		}
		setStateMenuIsOpen(false);
	}, [propsOnMenuClose]);
	const inputValue = propsInputValue !== undefined ? propsInputValue : stateInputValue;
	const menuIsOpen = propsMenuIsOpen !== undefined ? propsMenuIsOpen : stateMenuIsOpen;
	const value = propsValue !== undefined ? propsValue : stateValue;

	return {
		...restSelectProps,
		inputValue,
		menuIsOpen,
		onChange,
		onInputChange,
		onMenuClose,
		onMenuOpen,
		value: normalizeValue(value, isMulti),
	};
}
