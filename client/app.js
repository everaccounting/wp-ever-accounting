import {routes} from './routes';
import {Fragment} from "@wordpress/element";
import {HashRouter as Router, Route, Switch, Redirect} from 'react-router-dom';
import {COLLECTIONS_STORE_KEY} from "data";
import { NotificationContainer } from 'react-notifications';

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
			<NotificationContainer/>
		</Fragment>
	);
};

export default App;
