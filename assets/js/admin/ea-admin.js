import { render } from '@wordpress/element';
import domReady from '@wordpress/dom-ready';
import { __ } from '@wordpress/i18n';

function App() {
	return (
		<div className="remove-me">
			{ __(
				'Hello World!, I am assets->frontend->frontend.js file. Remove me to get started',
				'eaccounting-addon'
			) }
		</div>
	);
}

domReady( () => {
	const root = document.getElementById( 'wpbody-content' );
	return root ? render( <App />, root ) : null;
} );
