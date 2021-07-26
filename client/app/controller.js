/**
 * WordPress dependencies
 */
import { Suspense, lazy } from '@wordpress/element';
import { applyFilters } from '@wordpress/hooks';

/**
 * External dependencies
 */
import { Loading } from '@eaccounting/components';
import { getScreenFromPath, getHistory } from '@eaccounting/navigation';
import { __ } from '@wordpress/i18n';
import { parse, stringify } from 'qs';
import { find, isEqual, last, omit } from 'lodash';

const Overview = lazy( () => import( '../overview' ) );
const Items = lazy( () => import( '../items' ) );
const Sales = lazy( () => import( '../sales' ) );
const Expenses = lazy( () => import( '../expenses' ) );
const Banking = lazy( () => import( '../banking' ) );

export const getPages = () => {
	const pages = [];

	pages.push( {
		container: Overview,
		path: '/overview',
		wpOpenMenu: 'toplevel_page_eaccounting',
		navArgs: {},
		capability: 'manage_options',
	} );

	pages.push( {
		container: Items,
		path: '/items',
		wpOpenMenu: 'toplevel_page_eaccounting',
		navArgs: {},
		capability: 'manage_options',
	} );

	pages.push( {
		container: Sales,
		path: '/sales',
		wpOpenMenu: 'toplevel_page_eaccounting',
		navArgs: {},
		capability: 'manage_options',
	} );

	pages.push( {
		container: Expenses,
		path: '/expenses',
		wpOpenMenu: 'toplevel_page_eaccounting',
		navArgs: {},
		capability: 'manage_options',
	} );

	pages.push( {
		container: Banking,
		path: '/banking',
		wpOpenMenu: 'toplevel_page_eaccounting',
		navArgs: {},
		capability: 'manage_options',
	} );

	return applyFilters( 'EACCOUNTING_ADMIN_PAGES', pages );
};

export const Controller = ( props ) => {
	const { page, match, query, restProps } = props;
	const { url, params } = match;
	window.wpNavMenuUrlUpdate( query );
	window.wpNavMenuClassChange( page, url );
	return (
		<Suspense fallback={ <Loading text={ __( 'Please wait…' ) } /> }>
			<page.container
				params={ params }
				path={ url }
				pathMatch={ page.path }
				query={ query }
				{ ...restProps }
			/>
		</Suspense>
	);
};

/**
 * Update an anchor's link in sidebar to include persisted queries. Leave excluded screens
 * as is.
 *
 * @param {HTMLElement} item - Sidebar anchor link.
 */
export function updateLinkHref( item ) {
	if ( /admin.php\?page=eaccounting/.test( item.href ) ) {
		const search = last( item.href.split( '?' ) );
		const query = parse( search );
		const href = 'admin.php?' + stringify( Object.assign( query ) );
		// Replace the href so you can see the url on hover.
		item.href = href;
		item.onclick = ( e ) => {
			e.preventDefault();
			getHistory().push( href );
		};
	}
}

// Update's eaccounting links in wp-admin menu
window.wpNavMenuUrlUpdate = function ( query ) {
	Array.from(
		document.querySelectorAll( '#adminmenu a' )
	).forEach( ( item ) => updateLinkHref( item ) );
};

// When the route changes, we need to update wp-admin's menu with the correct section & current link
window.wpNavMenuClassChange = function ( page, url ) {
	Array.from( document.getElementsByClassName( 'current' ) ).forEach(
		function ( item ) {
			item.classList.remove( 'current' );
		}
	);
	const submenu = Array.from(
		document.querySelectorAll( '.wp-has-current-submenu' )
	);
	submenu.forEach( function ( element ) {
		element.classList.remove( 'wp-has-current-submenu' );
		element.classList.remove( 'wp-menu-open' );
		element.classList.remove( 'selected' );
		element.classList.add( 'wp-not-current-submenu' );
		element.classList.add( 'menu-top' );
	} );

	const pageUrl =
		url === '/'
			? 'admin.php?page=eaccounting'
			: 'admin.php?page=eaccounting&path=' + encodeURIComponent( url );
	const currentItemsSelector =
		url === '/'
			? `li > a[href$="${ pageUrl }"], li > a[href*="${ pageUrl }?"]`
			: `li > a[href*="${ pageUrl }"]`;
	const currentItems = document.querySelectorAll( currentItemsSelector );

	Array.from( currentItems ).forEach( function ( item ) {
		item.parentElement.classList.add( 'current' );
	} );

	if ( page.wpOpenMenu ) {
		const currentMenu = document.querySelector( '#' + page.wpOpenMenu );
		if ( currentMenu ) {
			currentMenu.classList.remove( 'wp-not-current-submenu' );
			currentMenu.classList.add( 'wp-has-current-submenu' );
			currentMenu.classList.add( 'wp-menu-open' );
			currentMenu.classList.add( 'current' );
		}
	}

	const wpWrap = document.querySelector( '#wpwrap' );
	wpWrap.classList.remove( 'wp-responsive-open' );
};
