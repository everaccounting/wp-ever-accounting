/**
 * WordPress dependencies
 */
import { Button } from '@wordpress/components';

export default function BusinessDetails( { goToNextStep, ...props } ) {
	return (
		<>
			SetupWizardHeader
			<Button onClick={ goToNextStep }> Go to Next</Button>
		</>
	);
}
