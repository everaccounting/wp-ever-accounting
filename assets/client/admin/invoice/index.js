/**
 * WordPress dependencies
 */
import { StrictMode, createRoot } from '@wordpress/element';
import domReady from '@wordpress/dom-ready';

/**
 * Internal dependencies
 */
import App from './app';
// import './style.scss';
domReady( () => {
	const domNode = document.getElementById( 'eac-invoice' );
	if ( ! domNode ) {
		return;
	}
	const root = createRoot( domNode );
	root.render(
		<StrictMode>
			<App />
		</StrictMode>
	);
} );
