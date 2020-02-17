import React from 'react';
import ReactDOM from 'react-dom';
import App from "./app";





if (document.getElementById('eaccounting')) {
	ReactDOM.render(
		<App/>,
		document.getElementById('eaccounting')
	);
}
