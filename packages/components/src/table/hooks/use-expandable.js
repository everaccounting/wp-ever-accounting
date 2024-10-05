/**
 * WordPress dependencies
 */
import { useState } from '@wordpress/element';

/**
 * External dependencies
 */
import { isEqual, noop, isEmpty } from 'lodash';

export function useExpandable( items = [], defaultExpandedItems = [], onChangeExpanded = noop ) {
	const [ expandedItems, setExpandedItems ] = useState( defaultExpandedItems );

	const isExpanded = ( item ) => expandedItems.includes( item );
	const isAllExpanded = ! isEmpty( items ) && items.every( ( item ) => isExpanded( item ) );
	const onExpandItem = ( item ) => {
		const newExpandedItems = isExpanded( item )
			? expandedItems.filter( ( expandedItem ) => ! isEqual( expandedItem, item ) )
			: [ ...expandedItems, item ];
		setExpandedItems( newExpandedItems );
		onChangeExpanded( newExpandedItems );
	};
	const onExpandAll = ( isChecked ) => {
		const newExpandedItems = isChecked ? items : [];
		setExpandedItems( newExpandedItems );
		onChangeExpanded( newExpandedItems );
	};

	return {
		expandedItems,
		isExpanded,
		isAllExpanded,
		onExpandItem,
		onExpandAll,
	};
}

export default useExpandable;
