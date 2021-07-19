/**
 * WordPress dependencies
 */
import { Suspense, lazy } from '@wordpress/element';
import { applyFilters } from '@wordpress/hooks';

/**
 * External dependencies
 */
import { Loading } from '@eaccounting/components';
import { __ } from '@wordpress/i18n';

const Overview = lazy( () => import( '../overview' ) );
const Sales = lazy( () => import( '../sales' ) );
const Banking = lazy( () => import( '../banking' ) );

export const getPages = () => {
	const pages = [];

	pages.push( {
		container: Overview,
		path: '/overview',
		wpOpenMenu: 'toplevel_page_accounting',
		navArgs: {},
		capability: 'manage_options',
	} );

	pages.push( {
		container: Sales,
		path: '/sales',
		wpOpenMenu: 'toplevel_page_accounting',
		navArgs: {},
		capability: 'manage_options',
	} );

	pages.push( {
		container: Banking,
		path: '/banking',
		wpOpenMenu: 'toplevel_page_accounting',
		navArgs: {},
		capability: 'manage_options',
	} );

	return applyFilters( 'EACCOUNTING_ADMIN_PAGES', pages );
};

export const Controller = ( props ) => {
	const { page, match, query, restProps } = props;
	const { url, params } = match;

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
