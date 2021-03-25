/**
 * WordPress dependencies
 */
// eslint-disable-next-line import/no-extraneous-dependencies
import { render } from '@wordpress/element';
import domReady from '@wordpress/dom-ready';
import { __ } from '@wordpress/i18n';
import {STORE_NAME} from '@eaccounting/data';
import {Lorem} from '@eaccounting/components';

/**
 * External dependencies
 */
/**
 * Internal dependencies
 */

function App() {
	console.log(STORE_NAME);
	return (
		<div className="remove-me">
			<Lorem/>
			{ __(
				'Hello World!, I am assets->frontend->frontend.js file. Remove me to get started',
				'eaccounting-addon'
			) }
			<image src={'../images/logo.png'} />
		</div>
	);
}

domReady( () => {
	const root = document.getElementById( 'wpbody-content' );
	return render( <App />, root );

} );
