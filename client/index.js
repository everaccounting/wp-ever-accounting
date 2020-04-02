/* global document, eAccountingi10n */
/**
 * WordPress dependencies
 */
import {render} from '@wordpress/element';
import domReady from '@wordpress/dom-ready';
import "@wordpress/notices";
/**
 * Internal dependencies
 */
import App from './app';
import './stylesheets/main.scss';

domReady(() => {
	const root = document.getElementById('eaccounting');
	return render(<App/>, root);
});

