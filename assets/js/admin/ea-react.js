/**
 * WordPress dependencies
 */
// eslint-disable-next-line import/no-extraneous-dependencies
import { render } from '@wordpress/element';
import domReady from '@wordpress/dom-ready';
import { __ } from '@wordpress/i18n';
import {STORE_NAME} from '@eaccounting/data';

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
			Lorem ipsum dolor sit amet, consectetur adipisicing elit. Ad dolore dolores est et neque! A accusantium, aliquid autem consequuntur earum hic inventore nobis non quasi quibusdam, recusandae rem suscipit voluptas.
		</div>
	);
}

domReady( () => {
	const root = document.getElementById( 'wpbody-content' );
	return render( <App />, root );

} );
