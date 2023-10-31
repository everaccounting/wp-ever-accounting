/**
 * External dependencies
 */
import classnames from 'classnames';
/**
 * WordPress dependencies
 */
import { Button, Dropdown, NavigableMenu, Icon } from '@wordpress/components';
import { useRef } from '@wordpress/element';
/**
 * Internal dependencies
 */
import './style.scss';

function isFunction( maybeFunc ) {
	return typeof maybeFunc === 'function';
}

const DropdownMenu = ( { label, children, menu, className, onToggle } ) => {
	// bind event for clicking outside the dropdown menu.
	const containerRef = useRef( null );

	if ( ! menu?.length && ! children ) {
		return null;
	}

	const renderDropdown = ( { onToggle: toggleHandlerOverride, isOpen } ) => {
		const toggleClassname = classnames( 'eac-dropdown-menu__toggle', {
			'is-opened': isOpen,
		} );
		return (
			<Button
				className={ toggleClassname }
				onClick={ ( e ) => {
					if ( onToggle ) {
						onToggle( e );
					}
					if ( toggleHandlerOverride ) {
						toggleHandlerOverride();
					}
				} }
				title={ label }
				aria-expanded={ isOpen }
				icon="ellipsis"
			>
				{ label && (
					<span className="eac-dropdown-menu__toggle-label">
						{ label }
					</span>
				) }
			</Button>
		);
	};
	const renderMenu = ( renderContentArgs ) => (
		<NavigableMenu className="eac-dropdown-menu__content" role="menu">
			{ children || menu( renderContentArgs ) }
		</NavigableMenu>
	);
	return (
		<div
			className={ classnames( className, 'eac-dropdown-menu' ) }
			ref={ containerRef }
		>
			<Dropdown
				contentClassName="eac-dropdown-menu__popover"
				position="bottom left"
				renderToggle={ renderDropdown }
				renderContent={ renderMenu }
			/>
		</div>
	);
};
export default DropdownMenu;
