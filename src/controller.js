import { Component, Fragment } from 'react';
import { parse, stringify } from 'qs';
import { find, isEqual, last, omit } from 'lodash';

import { getNewPath, getPersistedQuery, getHistory } from '@eaccounting/navigation';

import { applyFilters } from '@wordpress/hooks';
import Dashboard from './page/dashboard';
import Incomes from './page/incomes';
import Banking from './page/banking';
import Transactions from './page/transactions';
import Misc from './page/misc';

export const PAGES_FILTER = 'eaccounting_admin_pages_list';

export const getPages = () => {
	const pages = [
		{
			container: Dashboard,
			path: '/',
			wpOpenMenu: 'toplevel_page_eaccounting',
		},
		{
			container: Transactions,
			path: '/transactions',
			wpOpenMenu: 'toplevel_page_eaccounting',
		},
		{
			container: Incomes,
			path: '/incomes/:section/add',
			wpOpenMenu: 'toplevel_page_eaccounting',
		},
		{
			container: Incomes,
			path: '/incomes/:section/:id',
			wpOpenMenu: 'toplevel_page_eaccounting',
		},
		{
			container: Incomes,
			path: '/incomes/:section',
			wpOpenMenu: 'toplevel_page_eaccounting',
		},
		{
			container: Incomes,
			path: '/incomes',
			wpOpenMenu: 'toplevel_page_eaccounting',
		},
		{
			container: Banking,
			path: '/banking/:section',
			wpOpenMenu: 'toplevel_page_eaccounting',
		},
		{
			container: Banking,
			path: '/banking',
			wpOpenMenu: 'toplevel_page_eaccounting',
		},
		{
			container: Misc,
			path: '/misc/:section',
			wpOpenMenu: 'toplevel_page_eaccounting',
		},
		{
			container: Misc,
			path: '/misc',
			wpOpenMenu: 'toplevel_page_eaccounting',
		},
	];

	return applyFilters(PAGES_FILTER, pages);
};

export class Controller extends Component {
	constructor(props, context) {
		super(props, context);
	}

	componentDidMount() {
		window.document.documentElement.scrollTop = 0;
	}

	componentDidUpdate(prevProps) {
		const prevQuery = this.getQuery(prevProps.location.search);
		const prevBaseQuery = omit(this.getQuery(prevProps.location.search), 'paged');
		const baseQuery = omit(this.getQuery(this.props.location.search), 'paged');

		if (prevQuery.paged > 1 && !isEqual(prevBaseQuery, baseQuery)) {
			getHistory().replace(getNewPath({ paged: 1 }));
		}

		if (prevProps.match.url !== this.props.match.url) {
			window.document.documentElement.scrollTop = 0;
		}
	}

	getQuery(searchString) {
		if (!searchString) {
			return {};
		}

		const search = searchString.substring(1);
		return parse(search);
	}

	render() {
		const { page, match, location } = this.props;
		const { url, params } = match;
		const query = this.getQuery(location.search);
		// window.wpNavMenuUrlUpdate( query );
		window.wpNavMenuClassChange(page, url);
		return (
			<Fragment>
				<page.container params={params} path={url} pathMatch={page.path} query={query} />
			</Fragment>
		);
	}
}

/**
 * Update an anchor's link in sidebar to include persisted queries. Leave excluded screens
 * as is.
 *
 * @param {HTMLElement} item - Sidebar anchor link.
 * @param {Object} nextQuery - A query object to be added to updated hrefs.
 * @param {Array} excludedScreens - wc-admin screens to avoid updating.
 */
export function updateLinkHref(item, nextQuery, excludedScreens) {
	const isAccounting = /admin.php\?page=eaccounting/.test(item.href);
	if (isAccounting) {
		const search = last(item.href.split('?'));
		const page = last(item.href.split('#'));
		const query = parse(search);
		const screen = path.replace('eaccounting', '').replace('/', '');
		const isExcludedScreen = excludedScreens.includes(screen);
		const href = 'admin.php?' + stringify(Object.assign(query, isExcludedScreen ? {} : nextQuery));

		// Replace the href so you can see the url on hover.
		item.href = href;
		// item.onclick = ( e ) => {
		// 	e.preventDefault();
		// 	getHistory().push( href );
		// };
	}
}

// Update's wc-admin links in wp-admin menu
window.wpNavMenuUrlUpdate = function(query) {
	const excludedScreens = [];
	const nextQuery = getPersistedQuery(query);
	Array.from(document.querySelectorAll('#adminmenu a')).forEach(item =>
		updateLinkHref(item, nextQuery, excludedScreens)
	);
};

// When the route changes, we need to update wp-admin's menu with the correct section & current link
window.wpNavMenuClassChange = function(page, url) {
	Array.from(document.getElementsByClassName('current')).forEach(function(item) {
		item.classList.remove('current');
	});

	const submenu = Array.from(document.querySelectorAll('.wp-has-current-submenu'));
	submenu.forEach(function(element) {
		element.classList.remove('wp-has-current-submenu');
		element.classList.remove('wp-menu-open');
		element.classList.remove('selected');
		element.classList.add('wp-not-current-submenu');
		element.classList.add('menu-top');
	});

	const pageUrl = url === '/' ? 'admin.php?page=eaccounting' : 'admin.php?page=eaccounting#' + url;
	const currentItemsSelector =
		url === '/' ? `li > a[href$="${pageUrl}"], li > a[href*="${pageUrl}?"]` : `li > a[href*="${pageUrl}"]`;
	const currentItems = document.querySelectorAll(currentItemsSelector);

	Array.from(currentItems).forEach(function(item) {
		item.parentElement.classList.add('current');
	});

	if (page.wpOpenMenu) {
		const currentMenu = document.querySelector('#' + page.wpOpenMenu);
		currentMenu.classList.remove('wp-not-current-submenu');
		currentMenu.classList.add('wp-has-current-submenu');
		currentMenu.classList.add('wp-menu-open');
		currentMenu.classList.add('current');
	}
};
