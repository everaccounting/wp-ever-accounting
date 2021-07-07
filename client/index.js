/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */
import { render } from '@wordpress/element';
import domReady from '@wordpress/dom-ready';
import '@wordpress/notices';
import { DIST_URL } from '@eaccounting/data';

/**
 * Internal dependencies
 */
import App from './app';

// Modify webpack pubilcPath at runtime based on location of WordPress Plugin.
// eslint-disable-next-line no-undef,camelcase
__webpack_public_path__ = DIST_URL;

domReady( () => {
	const appRoot = document.getElementById( 'eaccounting-root' );
	render( <App />, appRoot );
} );
