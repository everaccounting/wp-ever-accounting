/**
 * External dependencies
 */
import { useLocation } from 'react-router-dom';
/**
 * WordPress dependencies
 */
import { useEffect } from '@wordpress/element';
import { usePrevious } from '@wordpress/compose';

export const useMenuFix = () => {
	const location = useLocation();
	const pathname = location.pathname;
	const prevPathname = usePrevious(pathname);
	// when anything changed in url, we need to scroll to top.
	useEffect(() => {
		if (pathname !== prevPathname) {
			window.scrollTo(0, 0);
		}
	}, [pathname, prevPathname]);

	const page = pathname.split('/')[1];
	const wpNavMenu = document.querySelector('#adminmenu');
	const currentMenu = wpNavMenu.querySelector('#toplevel_page_accounting');
	Array.from(wpNavMenu.getElementsByClassName('current')).forEach(function (item) {
		item.classList.remove('current');
	});

	const submenu = Array.from(wpNavMenu.querySelectorAll('.wp-has-current-submenu'));
	submenu.forEach(function (element) {
		element.classList.remove('wp-has-current-submenu');
		element.classList.remove('wp-menu-open');
		element.classList.remove('selected');
		element.classList.add('wp-not-current-submenu');
		element.classList.add('menu-top');
	});
	if (currentMenu) {
		currentMenu.classList.remove('wp-not-current-submenu');
		currentMenu.classList.add('wp-has-current-submenu');
		currentMenu.classList.add('wp-menu-open');
		currentMenu.classList.add('current');
	}

	const pageUrl =
		pathname === '/' ? 'admin.php?page=accounting#' : 'admin.php?page=accounting#/' + page;
	const currentItemsSelector =
		pathname === '/'
			? `li > a[href$="${pageUrl}"], li > a[href*="${pageUrl}?"]`
			: `li > a[href*="${pageUrl}"]`;

	const currentItems = wpNavMenu.querySelectorAll(currentItemsSelector);
	Array.from(currentItems).forEach(function (item) {
		item.parentElement.classList.add('current');
	});
	return null;
};
