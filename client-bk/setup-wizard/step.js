/**
 * Internal dependencies
 */
import routes from './routes';
/**
 * External dependencies
 */
import { find, indexOf } from 'lodash';
/**
 * External dependencies
 */
import { NavLink } from '@eaccounting/navigation';

/**
 * WordPress dependencies
 */
import { Suspense } from '@wordpress/element';
import { Button } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

// eslint-disable-next-line no-unused-vars
export default function Step({ match, steps, ...props }) {
	const { path } = match;
	const currentStep = find(steps, { path: path.replace('/', '') });
	const currentIndex = indexOf(steps, currentStep);
	const Container = props.container;

	const goBack = () => {
		console.log(currentStep);
		console.log(currentIndex);
		console.log('back');
	};

	const goNext = () => {
		console.log(currentStep);
		console.log(currentIndex);
		console.log('next');
	};

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
					{currentStep && (
						<div className="ea-setup__header">
							<h1>{currentStep.title}</h1>
						</div>
					)}
					<div className="ea-setup__body">
						<Container />
					</div>

					<div className="ea-setup__footer">
						<Button isDefault={true} onClick={goBack}>
							{__('Back')}
						</Button>
						<Button isPrimary={true} onClick={goNext}>
							{__('Next')}
						</Button>
					</div>
				</Suspense>
			</div>
		</>
	);
}
