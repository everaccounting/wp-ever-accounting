/**
 * WordPress dependencies
 */
import { render } from '@wordpress/element';
import domReady from '@wordpress/dom-ready';

/**
 * External dependencies
 */
import {
	HashRouter as Router,
	Redirect,
	Route,
	Switch,
} from '@eaccounting/router';

/**
 * Internal dependencies
 */
// eslint-disable-next-line no-undef
const { logo_url, dist_url } = ea_setupi10n;
import routes from './routes';
import './style.scss';
import Step from './step';

// eslint-disable-next-line no-undef
__webpack_public_path__ = `${dist_url}/`;

function Wizard() {
	return (
		<>
			<h1 className="ea-setup__logo">
				<a
					href="https://wpeveraccounting.com/"
					target="_blank"
					rel="noreferrer"
				>
					<img
						src={logo_url}
						alt="Ever Accounting"
						width={300}
						height={66}
					/>
				</a>
			</h1>

			<Router>
				<Switch>
					{routes.map((step) => {
						return (
							<Route
								key={step.path}
								path={'/' + step.path}
								render={(props) => (
									<Step
										container={step.container}
										{...props}
									/>
								)}
							/>
						);
					})}
					<Redirect from="*" to="/introduction" />
				</Switch>
			</Router>
		</>
	);
}

domReady(() => {
	const root = document.getElementById('ea-setup-wizard');
	return root ? render(<Wizard />, root) : null;
});
