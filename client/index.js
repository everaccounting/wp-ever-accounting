import {render} from '@wordpress/element';
import domReady from '@wordpress/dom-ready';

import './stylesheets/syle.scss';




__webpack_public_path__ = eaccounting_client_i10n.dist_url;
import App from './app';

domReady(() => {
	const root = document.getElementById('eaccounting');
	return render(<App/>, root);
});
