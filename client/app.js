import {routes} from './routes';
import {Fragment, Component} from "@wordpress/element";
import {HashRouter as Router, Route, Switch, Redirect} from 'react-router-dom';
import {NotificationContainer} from 'react-notifications';


//todo add preloader
class Page extends Component {
	constructor(props) {
		super(props);
	}

	componentDidMount() {
		window.scrollTo(0, 0);
	}

	render() {
		window.wpNavMenuClassChange(this.props.history.location.pathname);
		return (
			<this.props.container {...this.props}/>
		);
	}
}

const App = () => {
	return (
		<Fragment>
			<Router>
				<Switch>
					{routes.map(page => {
						return (
							<Route
								key={page.path}
								path={page.path}
								exact
								render={props => <Page container={page.container} {...props}/>}/>
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

/**
 * Hack for changing the sub-nav menu core classes for 'pages' And update menu link
 */
window.wpNavMenuClassChange = function (pathname) {
	// let hash = window.location.hash;
	// Clear currents
	Array.from(document.getElementsByClassName('current')).forEach(
		function (item) {
			item.classList.remove('current');
		}
	);
	console.log(pathname);
	const pageUrl = pathname === '/' ? 'admin.php?page=eaccounting#/' : 'admin.php?page=eaccounting#/' + pathname.split('/')[1];
	console.log(pageUrl);
	const currentItemsSelector = pathname === '/' ? `li > a[href$="${pageUrl}"], li > a[href*="${pageUrl}?"]` : `li > a[href*="${pageUrl}"]`;
	console.log(currentItemsSelector);
	const currentItems = document.querySelectorAll(currentItemsSelector);
	console.log(currentItems);

	Array.from(currentItems).forEach(function (item) {
		item.parentElement.classList.add('current');
	});
	const dashboard = document.querySelector('#toplevel_page_eaccounting .wp-first-item a');

	dashboard.href = dashboard.href.replace('#/', '') + '#/';
};

