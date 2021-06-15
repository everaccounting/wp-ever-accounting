/**
 * WordPress dependencies
 */
import { Suspense } from '@wordpress/element';
import { Spinner } from '@wordpress/components';
import { applyFilters } from '@wordpress/hooks';

export const PAGES_FILTER = 'eaccounting_admin_pages_list';

const Homescreen = () => {
	return <>Home</>;
};

const SetupWizard = () => {
	return <>SetupWizard</>;
};

export const getPages = () => {
	const pages = [];

	pages.push({
		container: Homescreen,
		path: '/',
		wpOpenMenu: 'toplevel_page_woocommerce',
		navArgs: {
			id: 'woocommerce-home',
		},
		capability: 'manage_woocommerce',
	});

	pages.push({
		container: SetupWizard,
		path: '/setup',
		wpOpenMenu: 'toplevel_page_woocommerce',
		navArgs: {
			id: 'woocommerce-home',
		},
		capability: 'manage_woocommerce',
	});

	return applyFilters(PAGES_FILTER, pages);
};

export const Controller = (props) => {
	const { page, match, query } = props;
	const { url, params } = match;

	return (
		<Suspense fallback={<Spinner />}>
			<page.container
				params={params}
				path={url}
				pathMatch={page.path}
				query={query}
			/>
		</Suspense>
	);
};
