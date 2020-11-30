import {render} from '@wordpress/element';
import domReady from '@wordpress/dom-ready';
import App from './app';

import './style.scss';

const root = document.getElementById('eaccounting-app');
if (root) {
	domReady(() => {
		return render(<App/>, root);
	});
}

