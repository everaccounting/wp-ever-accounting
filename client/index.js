/**
 * WordPress dependencies
 */
import { render } from '@wordpress/element';
import domReady from '@wordpress/dom-ready';
/**
 * External dependencies
 */
// import { Loading } from '@eaccounting/components';
/**
 * Internal dependencies
 */
function App() {
	return (
		<>
			Lorem ipsum dolor sit amet, consectetur adipisicing elit. Alias,
			expedita hello new a!
		</>
	);
}

domReady(() => {
	const root = document.getElementById('ea-react');
	return render(<App />, root);
});
