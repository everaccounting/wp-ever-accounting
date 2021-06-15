/**
 * WordPress dependencies
 */
import { render } from '@wordpress/element';
import domReady from '@wordpress/dom-ready';
import '@wordpress/notices';

/**
 * Internal dependencies
 */
import { PageLayout } from './layout';
/**
 * External dependencies
 */
import { withCurrentUser, withSettings } from '@eaccounting/data';

// Modify webpack pubilcPath at runtime based on location of WordPress Plugin.
// eslint-disable-next-line no-undef,camelcase
__webpack_public_path__ = window.eaccountingi10n.dist_url;
const current_user = window.eaccountingi10n.current_user;

const root = document.getElementById( 'eaccounting-root' );
let HydratedPageLayout = PageLayout;
if ( current_user ) {
	HydratedPageLayout = withCurrentUser( current_user )( PageLayout );
}
HydratedPageLayout = withSettings()( HydratedPageLayout );

domReady( () => {
	return root ? render( <HydratedPageLayout />, root ) : null;
} );
