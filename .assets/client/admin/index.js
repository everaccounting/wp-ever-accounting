import { StrictMode, createRoot } from '@wordpress/element';
import domReady from '@wordpress/dom-ready';
import App from './app';

domReady( () => {
	const domNode = document.getElementById( 'eac-tax-rates' );
	if ( ! domNode ) {
		return;
	}
	const root = createRoot( domNode );
	root.render(
		<StrictMode>
			<App />
		</StrictMode>
	);
});
