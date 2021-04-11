/**
 * WordPress dependencies
 */
import { render } from '@wordpress/element';
import domReady from '@wordpress/dom-ready';
import { __ } from '@wordpress/i18n';
// eslint-disable-next-line import/no-unresolved
/**
 * External dependencies
 */
import { STORE_NAME } from '@eaccounting/data';
import { Lorem } from '@eaccounting/components';

function App() {
	console.log(STORE_NAME);
	return (
		<div className="remove-me">
			<Lorem />
			{__(
				'Hello World!, I am assets->frontend->frontend.js file. Remove me to get started',
				'eaccounting-addon'
			)}
			<image src={'../images/logo.png'} />
		</div>
	);
}

domReady(() => {
	const root = document.getElementById('wpbody-content');
	return render(<App />, root);
});
