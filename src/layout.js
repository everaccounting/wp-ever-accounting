import {HashRouter as Router, Route, Switch} from 'react-router-dom';
import { Component } from 'react';
import { get, isFunction } from 'lodash';

import { Controller, getPages, PAGES_FILTER } from './controller';

export default class Layout extends Component {
	render() {
		return (
			<Router>
				<Switch>
					{ getPages().map( ( page ) => {
						return (
							<Route
								key={ page.path }
								path={ page.path }
								exact
								render={ ( props ) => (
									<Controller page={ page } { ...props } />
								) }
							/>
						);
					} ) }
				</Switch>
			</Router>
		);
	}
}