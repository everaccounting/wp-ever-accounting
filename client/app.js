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
		window.wpNavMenuClassChange();
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
window.wpNavMenuClassChange = function () {
	let hash = window.location.hash;
	// Clear currents
	Array.from(document.getElementsByClassName('current')).forEach(
		function (item) {
			item.classList.remove('current');
		}
	);
	const pageUrl = hash === '#/' ? 'admin.php?page=eaccounting#/' : 'admin.php?page=eaccounting#/' + hash.split('/')[1];
	const currentItemsSelector = hash === '#/' ? `li > a[href$="${pageUrl}"], li > a[href*="${pageUrl}?"]` : `li > a[href*="${pageUrl}"]`;
	const currentItems = document.querySelectorAll(currentItemsSelector);

	Array.from(currentItems).forEach(function (item) {
		item.parentElement.classList.add('current');
	});
	const dashboard = document.querySelector('#toplevel_page_eaccounting .wp-first-item a');

	dashboard.href = dashboard.href.replace('#/', '') + '#/';
};

