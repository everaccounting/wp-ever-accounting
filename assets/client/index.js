import { render } from '@wordpress/element';
import domReady from '@wordpress/dom-ready';
import { Fragment, Component } from '@wordpress/element';

// __webpack_public_path__ = `${PLUGIN_URL}/`;

class App extends Component {
	render() {
		return (
			<Fragment>
				Hello world
				<Button isPrimary={true}>Button</Button>
			</Fragment>
		);
	}
}

domReady(() => {
	const root = document.getElementById('eaccounting');
	return render(<App />, root);
});
