import {render} from '@wordpress/element';
import domReady from '@wordpress/dom-ready';
import {Fragment, Component} from '@wordpress/element';

__webpack_public_path__ = eaccounting_client_i10n.dist_url;
import Example from './example';

import './stylesheets/syle.scss'

class App extends Component {

	render() {
		return (
			<Fragment>
				<Example/>
			</Fragment>
		);
	}
}

domReady(() => {
	const root = document.getElementById('eaccounting');
	return render(<App/>, root);
});
