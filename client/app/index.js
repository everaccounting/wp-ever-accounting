/* global everAccountingData */
import { render, StrictMode } from '@wordpress/element';
import domReady from '@wordpress/dom-ready';

// Lazy load app.
import App from './app';

domReady(() => {
	const appRoot = document.getElementById('eac-root');
	if (appRoot) {
		render(
			<StrictMode>
				<App />
			</StrictMode>,
			appRoot
		);
	}
});
