/**
 * External dependencies
 */
import {
	Navigation,
	NavigationMenu,
	NavigationGroup,
	NavigationItem,
} from '@eaccounting/components';
/**
 * WordPress dependencies
 */
import { DropdownMenu, Button } from '@wordpress/components';
import { Fragment } from '@wordpress/element';
import { isEmpty } from 'lodash';
/**
 * External dependencies
 */
import { useUser } from '@eaccounting/data';
/**
 * Internal dependencies
 */
import Dropdown from './dropdown';
import Item from './item';
import { normalizeMenuItems } from './utils';
export default function ( { menuItems, props } ) {
	const { currentUserCan } = useUser();
	const menu = normalizeMenuItems( menuItems, currentUserCan );
	console.log( menu );
	return (
		<div className="eaccounting-navigation__wrapper">
			<NavigationMenu
				role="tablist"
				orientation="horizontal"
				className="eaccounting-navigation__menu"
			>
				{ menu &&
					Object.values( menu ).map( ( menuItem ) => {
						if ( menuItem.component ) {
							const { component: Comp, options } = menuItem;
							return <Comp key={ menuItem.id } { ...options } />;
						}
						const MenuItem = ! isEmpty( menuItem.submenu )
							? Dropdown
							: Item;
						return <MenuItem key={ menuItem.id } { ...menuItem } />;
					} ) }
			</NavigationMenu>
		</div>
	);
}
