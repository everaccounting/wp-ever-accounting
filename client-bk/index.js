/**
 * WordPress dependencies
 */
import { render } from '@wordpress/element';
import domReady from '@wordpress/dom-ready';

/**
 * Internal dependencies
 */
import { PageLayout } from './layout';

// Modify webpack pubilcPath at runtime based on location of WordPress Plugin.
// eslint-disable-next-line no-undef,camelcase
__webpack_public_path__ = global.eaccountingi10n.dist_url;

const root = document.getElementById('ea-react');

domReady(() => {
	return root ? render(<PageLayout />, root) : null;
});
