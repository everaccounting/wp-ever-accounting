/* global document, eAccountingi10n */
/**
 * WordPress dependencies
 */
import {render} from '@wordpress/element';
import domReady from '@wordpress/dom-ready';
import '@wordpress/notices';
import { Fragment, Component } from '@wordpress/element';

// import {PLUGIN_URL} from "@eaccounting/data";
/**
 * Internal dependencies
 */
// import App from './app';
// import './stylesheets/main.scss';

// __webpack_public_path__ = `${PLUGIN_URL}/`;

import {Button} from "@wordpress/components";
import './style.scss';

class App extends Component{
	render(){
		return(
			<Fragment>
				Hello world
				<Button isPrimary={true}>Button</Button>
			</Fragment>
		)
	}
}


domReady(() => {
	const root = document.getElementById('eaccounting');
	return render(<App/>, root);
});
