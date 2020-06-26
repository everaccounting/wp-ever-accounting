/* global document, eAccountingi10n */
/**
 * WordPress dependencies
 */
import {render} from '@wordpress/element';
import domReady from '@wordpress/dom-ready';
import '@wordpress/notices';
import {PLUGIN_URL} from "@eaccounting/data";
/**
 * Internal dependencies
 */
import App from './app';
import './stylesheets/main.scss';

__webpack_public_path__ = `${PLUGIN_URL}/`;

domReady(() => {
	const root = document.getElementById('eaccounting');
	return render(<App/>, root);
});
