export function handleInputChange(inputValue, actionMeta, onInputChange) {
	if (onInputChange) {
		const newValue = onInputChange(inputValue, actionMeta);
		if (typeof newValue === 'string') return newValue;
	}
	return inputValue;
}

// based on type of is multi or not sanitize the value.
export function normalizeValue(value, isMulti) {
	if (isMulti) {
		return Array.isArray(value) && value.length ? value : [];
	}
	return value || null;
}
