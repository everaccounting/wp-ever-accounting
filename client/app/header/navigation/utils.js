/**
 * External dependencies
 */
import { getHistory } from '@eaccounting/navigation';

/**
 * Normalize an array of menu items by their properties.
 *
 * @param {Array} menuItems Array of menu items.
 * @param {Function} currentUserCan Function to check.
 * @return {Array} Normalized menu items.
 */
export const normalizeMenuItems = (
	menuItems,
	currentUserCan = ( x ) => x
) => {
	return menuItems
		.map( ( menuItem ) =>
			Object.assign(
				{
					id: '',
					title: '',
					parent: '',
					group: '',
					capability: '',
					url: '',
					order: 10,
					icon: '',
					onClick: ( x ) => x,
					matchExpression: '',
				},
				menuItem,
				{
					order: menuItem.order ? menuItem.order : 10,
					parent: menuItem.parent ? menuItem.parent : menuItem.id,
				}
			)
		)
		.sort( ( a, b ) => {
			if ( a.order === b.order ) {
				return a.title.localeCompare( b.title );
			}
			return a.order - b.order;
		} )
		.filter(
			( menuItem ) =>
				( menuItem.capability &&
					currentUserCan( menuItem.capability ) ) ||
				! menuItem.capability
		)
		.reduce( ( acc, item ) => {
			if ( ! acc[ item.parent ] ) {
				acc[ item.parent ] = { submenu: {} };
			}

			if ( item.parent === item.id ) {
				acc[ item.parent ] = { ...acc[ item.parent ], ...item };
			}

			if ( item.parent !== item.id ) {
				if ( ! acc[ item.parent ].submenu[ item.group ] ) {
					acc[ item.parent ].submenu = {
						...acc[ item.parent ].submenu,
						[ item.group ]: [],
					};
				}
				acc[ item.parent ].submenu[ item.group || [] ].push( item );
			}

			return acc;
		}, [] );
};

export const handleClick = ( menuItem ) => {
	const { url, onClick = ( x ) => x } = menuItem;
	if ( !! url ) {
		getHistory().push( url );
	}
	onClick();
};
