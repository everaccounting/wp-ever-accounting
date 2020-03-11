import {routes} from './routes';
import {HashRouter as Router, Route, Switch, Redirect} from 'react-router-dom';

const App = () => {
	return (
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
	);
};

export default App;
