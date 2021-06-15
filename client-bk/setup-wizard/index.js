/**
 * WordPress dependencies
 */
import {
	createElement,
	render,
	Suspense,
	useState,
	useEffect,
} from '@wordpress/element';
import domReady from '@wordpress/dom-ready';
/**
 * External dependencies
 */

/**
 * Internal dependencies
 */
// eslint-disable-next-line no-undef
const { logo_url, dist_url } = ea_setupi10n;
import getSteps from './steps';
import './style.scss';
import { getQuery, updateQueryString } from '@eaccounting/navigation';

// eslint-disable-next-line no-undef
__webpack_public_path__ = `${dist_url}/`;

function SetupWizard() {
	const [currentStep, setCurrentStep] = useState(getSteps()[0]);

	useEffect(() => {
		const { step } = getQuery();
		setCurrentStep(getSteps().find((s) => s.key === step) || getSteps()[0]);
	}, []);

	const goToNextStep = async () => {
		// const { activePlugins, dismissedTasks, updateOptions } = this.props;
		const currentStepIndex = getSteps().findIndex(
			(s) => s.key === currentStep.key
		);

		const nextStep = getSteps()[currentStepIndex + 1];

		if (typeof nextStep === 'undefined') {
			// this.completeProfiler();
			return;
		}

		return updateQueryString({ step: nextStep.key });
	};

	const container = createElement(currentStep.container, {
		goToNextStep,
	});

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

			<div>
				<Suspense fallback={'loading'}>{container}</Suspense>
			</div>
		</>
	);
}

domReady(() => {
	const root = document.getElementById('ea-setup-wizard');
	return root ? render(<SetupWizard />, root) : null;
});
