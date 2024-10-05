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
	activeStyle = {
		backgroundColor: 'var(--wp-components-color-accent, var(--wp-admin-theme-color, #3858e9))',
		color: '#fff',
	},
	item,
	tooltipText,
} ) => {
	function renderListItem() {
		return (
			<li
				style={ isActive ? activeStyle : {} }
				{ ...getItemProps( { item, index } ) }
				className="eac-select-control__menu-item"
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
