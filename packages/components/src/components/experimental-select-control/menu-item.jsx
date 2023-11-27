/**
 * External dependencies
 */
/**
 * WordPress dependencies
 */
import { Tooltip } from '@wordpress/components';
export const MenuItem = ( {
	children,
	getItemProps,
	index,
	isActive,
	activeStyle = { backgroundColor: '#bde4ff' },
	item,
	tooltipText,
} ) => {
	function renderListItem() {
		return (
			<li
				style={ isActive ? activeStyle : {} }
				{ ...getItemProps( { item, index } ) }
				className="woocommerce-experimental-select-control__menu-item"
			>
				{ children }
			</li>
		);
	}
	if ( tooltipText ) {
		return (
			<Tooltip text={ tooltipText } position="top center">
				{ renderListItem() }
			</Tooltip>
		);
	}
	return renderListItem();
};
