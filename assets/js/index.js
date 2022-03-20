/**
 * External dependencies
 */
import 'core-js/stable';

/**
 * WordPress dependencies
 */
import { render } from '@wordpress/element';
import domReady from '@wordpress/dom-ready';

/**
 * Internal dependencies
 */
import App from './app';

domReady( () => {
	const root = document.getElementById( 'eaccounting-root' );
	return render( <App />, root );
} );
