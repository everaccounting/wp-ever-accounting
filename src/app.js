/**
 * External dependencies
 */

import React from 'react';
import {Provider} from 'react-redux';

/**
 * Internal dependencies
 */

import createReduxStore from 'state';
import { getInitialState } from 'state/initial';
import Layout from './layout';

const App = () => (
	<Provider store={createReduxStore(getInitialState())}>
			<Layout/>
	</Provider>
);

export default App;
