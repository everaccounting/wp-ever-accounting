/* global document, eAccountingi10n */
import React from 'react';
import ReactDOM from 'react-dom';
import App from "./app";

import './stylesheets/main.scss';

if (document.getElementById('eaccounting')) {
	ReactDOM.render(
		<App/>,
		document.getElementById('eaccounting')
	);

}
