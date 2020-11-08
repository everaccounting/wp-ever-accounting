import {render} from '@wordpress/element';
import domReady from '@wordpress/dom-ready';
import '@wordpress/notices';
import App from './app';
import './style.scss';
import {ASSET_URL} from "@eaccounting/data";

__webpack_public_path__ = `${ASSET_URL}/dist/`;

const root = document.getElementById('eaccounting-invoice');
if (root) {
	domReady(() => {
		return render(<App/>, root);
	});
}

