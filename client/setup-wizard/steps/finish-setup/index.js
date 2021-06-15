/**
 * WordPress dependencies
 */
import { Button } from '@wordpress/components';

export default function FinishSetup( { goToNextStep, ...props } ) {
	return (
		<>
			FinishSetup
			<Button onClick={ goToNextStep }> Go to Next</Button>
		</>
	);
}
