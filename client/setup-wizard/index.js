/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { applyFilters } from '@wordpress/hooks';
import { createElement, useState } from '@wordpress/element';

/**
 * Internal dependencies
 */
import BusinessDetails from './steps/business-details';
import CurrencySetup from './steps/currency-setup';
import FinishSetup from './steps/finish-setup';

import SetupWizardHeader from './header';
/**
 * External dependencies
 */
import { Redirect } from '@eaccounting/navigation';

export default function SetupWizard( props ) {
	const [ completedSteps, setCompletedSteps ] = useState( [] );

	const getSteps = () => {
		const steps = [
			{
				key: 'business-details',
				container: BusinessDetails,
				label: __( 'Business Details' ),
			},
			{
				key: 'currency-setup',
				container: CurrencySetup,
				label: __( 'Currency Setup' ),
			},
			{
				key: 'finish-details',
				container: FinishSetup,
				label: __( 'Finish Details' ),
			},
		];

		return applyFilters( 'eaccounting_setup_wizard_steps', steps );
	};

	const getCurrentStep = () => {
		const { step } = props.query;
		const currentStep = getSteps().find( ( s ) => s.key === step );

		if ( ! currentStep ) {
			return getSteps()[ 0 ];
		}

		return currentStep;
	};

	const goToNextStep = async () => {
		const currentStep = getCurrentStep();
		const currentStepIndex = getSteps().findIndex(
			( s ) => s.key === currentStep.key
		);
		const nextStep = getSteps()[ currentStepIndex + 1 ];

		if ( typeof nextStep === 'undefined' ) {
			return completeSetup();
		}

		return Redirect( { step: nextStep.key } );
	};

	const skipStep = () => {
		console.log( 'SKIP' );
	};

	const completeSetup = () => {
		console.log( 'COMPLETE' );
	};

	const { query } = props;
	const step = getCurrentStep();
	const container = createElement( step.container, {
		query,
		step,
		goToNextStep,
		skipStep,
	} );

	return (
		<>
			<SetupWizardHeader steps={ getSteps() } currentStep={ step } />
			<div className="eaccoutning-setup-wizard">{ container }</div>
		</>
	);
}
