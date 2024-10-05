/**
 * WordPress dependencies
 */
import { useState } from '@wordpress/element';

/**
 * External dependencies
 */
import { isEqual, uniq, noop, isEmpty } from 'lodash';

export function useSelection( items = [], defaultSelectedItems = [], onSelectionChange = noop ) {
	const [ selectedItems, setSelectedItems ] = useState( defaultSelectedItems );

	const isSelected = ( item ) => selectedItems.includes( item );
	const isAllSelected = ! isEmpty( items ) && items.every( ( item ) => isSelected( item ) );
	const onSelectItem = ( isChecked, item ) => {
		const newSelectedItems = isChecked
			? uniq( [ ...selectedItems, item ] )
			: selectedItems.filter( ( selectedItem ) => ! isEqual( selectedItem, item ) );

		setSelectedItems( newSelectedItems );
		onSelectionChange( newSelectedItems );
	};
	const onSelectAll = ( isChecked ) => {
		const newSelectedItems = isChecked ? items : [];
		setSelectedItems( newSelectedItems );
		onSelectionChange( newSelectedItems );
	};

	return {
		selectedItems,
		isSelected,
		isAllSelected,
		onSelectItem,
		onSelectAll,
	};
}

export default useSelection;
