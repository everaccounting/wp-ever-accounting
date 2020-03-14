import {routes} from './routes';
import {Fragment} from "@wordpress/element";
import {HashRouter as Router, Route, Switch, Redirect} from 'react-router-dom';
import {COLLECTIONS_STORE_KEY} from "data";

const App = () => {
	return (
		<Fragment>
			<Router>
				<Switch>
					{routes.map(page => {
						return (
							<Route key={page.path} path={page.path} exact
								   render={props => <page.container page={page} {...props} />}/>
						);
					})}
					<Redirect from="*" to="/"/>
				</Switch>
			</Router>
		</Fragment>
	);
};

export default App;
