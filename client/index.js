/**
 * WordPress dependencies
 */
import { render } from '@wordpress/element';
import domReady from '@wordpress/dom-ready';
/**
 * External dependencies
 */
import { Loading, EntitySelect } from '@eaccounting/components';

function App() {
	return (
		<>
			Lorem ipsum dolor sit amet, consectetur adipisicing elit. Alias,
			expedita!
			{/*<EntitySelect entity={'category'} query={{ type: 'income' }} />*/}
			<Loading />
		</>
	);
}

domReady(() => {
	const root = document.getElementById('ea-react');
	return render(<App />, root);
});
