/**
 * WordPress dependencies
 */
import { render } from '@wordpress/element';
import domReady from '@wordpress/dom-ready';
import '@wordpress/notices';
/**
 * External dependencies
 */
import {
	DIST_URL,
	getSiteData,
	withCurrentUserHydration,
	withCurrencyHydration,
} from '@eaccounting/data';

/**
 * Internal dependencies
 */
import { App, EmbeddedApp } from './app';
import './styles/index.scss';

// Modify webpack pubilcPath at runtime based on location of WordPress Plugin.
// eslint-disable-next-line no-undef,camelcase
__webpack_public_path__ = DIST_URL;
const hydrateUser = getSiteData('user_data');
const hydrateCurrencies = getSiteData('currencies', []);
domReady(() => {
	const appRoot = document.getElementById('eaccounting-root');
	const embeddedRoot = document.getElementById('eaccounting-embedded-root');
	if (appRoot) {
		let HydratedApp = App;
		if (hydrateUser) {
			HydratedApp = withCurrentUserHydration(hydrateUser)(App);
		}
		HydratedApp = withCurrencyHydration(hydrateCurrencies)(HydratedApp);
		render(<HydratedApp />, appRoot);
	} else if (embeddedRoot) {
		let HydratedEmbeddedApp = EmbeddedApp;
		if (hydrateUser) {
			HydratedEmbeddedApp =
				withCurrentUserHydration(hydrateUser)(HydratedEmbeddedApp);
		}
		HydratedEmbeddedApp =
			withCurrencyHydration(hydrateCurrencies)(HydratedEmbeddedApp);
		render(<HydratedEmbeddedApp />, embeddedRoot);
		embeddedRoot.classList.remove('is-embed-loading');
	}
});
