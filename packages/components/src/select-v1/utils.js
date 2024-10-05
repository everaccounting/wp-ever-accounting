function isDefaultItemType( item ) {
	return Boolean( item ) && item.label !== undefined && item.value !== undefined;
}
export const defaultGetItemLabel = ( item ) => {
	if ( isDefaultItemType( item ) ) {
		return item.label;
	}
	return '';
};
export const defaultGetItemValue = ( item ) => {
	if ( isDefaultItemType( item ) ) {
		return item.value;
	}
	return '';
};
export const defaultGetFilteredItems = ( allItems, inputValue, selectedItems, getOptionLabel ) => {
	const escapedInputValue = inputValue.replace( /[.*+?^${}()|[\]\\]/g, '\\$&' );
	const re = new RegExp( escapedInputValue, 'gi' );
	return allItems.filter( ( item ) => {
		return selectedItems.indexOf( item ) < 0 && re.test( getOptionLabel( item ).toLowerCase() );
	} );
};
