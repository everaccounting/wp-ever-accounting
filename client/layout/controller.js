/**
 * WordPress dependencies
 */
import { Suspense, lazy } from '@wordpress/element';
import { Spinner } from '@wordpress/components';
import { applyFilters } from '@wordpress/hooks';

const SetupWizard = lazy( () => import( '../setup-wizard' ) );
const Banking = lazy( () => import( '../banking' ) );

export const PAGES_FILTER = 'eaccounting_admin_pages_list';

const Homescreen = () => {
	return <>Home</>;
};

export const getPages = () => {
	const pages = [];

	pages.push( {
		container: Homescreen,
		path: '/',
		wpOpenMenu: 'toplevel_page_accounting',
		navArgs: {},
		capability: 'manage_options',
	} );

	pages.push( {
		container: Banking,
		path: '/banking',
		wpOpenMenu: '',
		navArgs: '',
		capability: 'manage_options',
	} );

	pages.push( {
		container: SetupWizard,
		path: '/setup',
		wpOpenMenu: '',
		navArgs: '',
		capability: 'manage_options',
	} );

	return applyFilters( PAGES_FILTER, pages );
};

export const Controller = ( props ) => {
	const { page, match, query, restProps } = props;
	const { url, params } = match;

	return (
		<Suspense fallback={ <Spinner /> }>
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
