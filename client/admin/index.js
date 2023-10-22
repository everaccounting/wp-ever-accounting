/**
 * WordPress dependencies
 */
import { StrictMode, createRoot } from '@wordpress/element';
import domReady from '@wordpress/dom-ready';

/**
 * External dependencies
 */
import { HashRouter as Router } from 'react-router-dom';

/**
 * Internal dependencies
 */
import App from './app';
import './style.scss';

// eslint-disable-next-line no-undef
__webpack_public_path__ = eac_vars.asset_url + '/client/';

domReady( () => {
	const domNode = document.getElementById( 'eac-app' );
	if ( ! domNode ) {
		return;
	}
	const root = createRoot( domNode );
	root.render(
		<StrictMode>
			<Router>
				<App />
			</Router>
		</StrictMode>
	);
} );
