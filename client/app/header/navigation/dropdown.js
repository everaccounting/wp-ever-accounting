/**
 * External dependencies
 */
/**
 * WordPress dependencies
 */
import {
	DropdownMenu,
	MenuGroup,
	MenuItemsChoice,
} from '@wordpress/components';
import { find } from 'lodash';
/**
 * Internal dependencies
 */
import { handleClick } from './utils';

export default function Dropdown( props ) {
	const { id, icon, title, className, submenu } = props;
	console.log( 'submenu', submenu );
	return (
		<DropdownMenu id={ id } icon={ icon } label={ title } text={ title }>
			{ ( { onClose } ) => (
				<>
					{ submenu &&
						Object.keys( submenu ).map( ( group, i ) => {
							const items = submenu[ group ];
							return (
								<MenuGroup
									key={ `MenuGroup-${ id }-${ group }-${ i }` }
									label={ group }
								>
									<MenuItemsChoice
										key={ `MenuItemsChoice-${ id }-${ group }-${ i }` }
										onSelect={ ( id ) => {
											const cur = find( items, { id } );
											onClose();
											if ( cur ) {
												handleClick( cur );
											}
										} }
										choices={ items.map( ( item ) => {
											return {
												value: item.id,
												label: (
													<>
														{ item.icon }
														{ item.title }
													</>
												),
											};
										} ) }
									/>
								</MenuGroup>
							);
						} ) }
				</>
			) }
		</DropdownMenu>
	);
}
