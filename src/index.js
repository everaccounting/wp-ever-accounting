/* global document, eAccountingi10n */
import { render } from '@wordpress/element';
import App from './app';
import domReady from '@wordpress/dom-ready';
import './stylesheets/main.scss';
import '@wordpress/notices';
domReady(() => {
	const root = document.getElementById('eaccounting');
	return render(<App />, root);
});
