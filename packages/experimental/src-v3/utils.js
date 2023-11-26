export function handleInputChange(inputValue, actionMeta, onInputChange) {
	if (onInputChange) {
		const newValue = onInputChange(inputValue, actionMeta);
		if (typeof newValue === 'string') return newValue;
	}
	return inputValue;
}
