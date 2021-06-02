/**
 * Internal dependencies
 */
import routes from './routes';
/**
 * External dependencies
 */
import { NavLink } from '@eaccounting/router';

/**
 * WordPress dependencies
 */
import { Suspense } from '@wordpress/element';
// eslint-disable-next-line no-unused-vars
export default function Step({ match, ...props }) {
	const Container = props.container;
	return (
		<>
			<ol className="ea-setup__steps">
				{routes.map((step) => {
					return (
						<li key={step.path}>
							<NavLink to={step.path}>{step.title}</NavLink>
						</li>
					);
				})}
			</ol>

			<div className="ea-setup__content">
				<Suspense fallback={'loading'}>
					<Container />
				</Suspense>
			</div>
		</>
	);
}
