/**
 * WordPress dependencies
 */
import { render } from '@wordpress/element';
import domReady from '@wordpress/dom-ready';
import Customers from "./customers";
function App() {
	return (
		<>
			<Customers/>
		</>
	);
}

domReady( () => {
	const root = document.getElementById( 'ea-react' );
	return render( <App />, root );

} );
