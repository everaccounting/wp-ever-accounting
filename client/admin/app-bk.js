/**
 * External dependencies
 */
import { useLocation } from 'react-router-dom';
/**
 * WordPress dependencies
 */
import { SlotFillProvider, Popover, Slot } from '@wordpress/components';
import { useEffect } from '@wordpress/element';
/**
 * Internal dependencies
 */
import Routes from './routes';
import { Header, Footer, Main } from './layout';

const useMenuFix = () => {
	const location = useLocation();
	const pathname = location.pathname;
	const page = pathname.split( '/' )[ 1 ];
	const wpNavMenu = document.querySelector( '#adminmenu' );
	const currentMenu = wpNavMenu.querySelector( '#toplevel_page_eac-admin' );
	Array.from( wpNavMenu.getElementsByClassName( 'current' ) ).forEach(
		function ( item ) {
			item.classList.remove( 'current' );
		}
	);

	const submenu = Array.from(
		wpNavMenu.querySelectorAll( '.wp-has-current-submenu' )
	);
	submenu.forEach( function ( element ) {
		element.classList.remove( 'wp-has-current-submenu' );
		element.classList.remove( 'wp-menu-open' );
		element.classList.remove( 'selected' );
		element.classList.add( 'wp-not-current-submenu' );
		element.classList.add( 'menu-top' );
	} );
	if ( currentMenu ) {
		currentMenu.classList.remove( 'wp-not-current-submenu' );
		currentMenu.classList.add( 'wp-has-current-submenu' );
		currentMenu.classList.add( 'wp-menu-open' );
		currentMenu.classList.add( 'current' );
	}

	const pageUrl =
		pathname === '/'
			? 'admin.php?page=eac-admin#'
			: 'admin.php?page=eac-admin#/' + page;
	const currentItemsSelector =
		pathname === '/'
			? `li > a[href$="${ pageUrl }"], li > a[href*="${ pageUrl }?"]`
			: `li > a[href*="${ pageUrl }"]`;

	const currentItems = wpNavMenu.querySelectorAll( currentItemsSelector );
	Array.from( currentItems ).forEach( function ( item ) {
		item.parentElement.classList.add( 'current' );
	} );
	return null;
};

export function App() {
	useMenuFix();

	useEffect( () => {
		window.document.documentElement.scrollTop = 0;
	}, [] );

	return (
		<div className="eac-layout">
			<SlotFillProvider>
				<Header />
				<Main>
					<Routes />
				</Main>
				<Footer />
				<Slot name="app-footer" />
				<Popover.Slot />
			</SlotFillProvider>
		</div>
	);
}

export default App;