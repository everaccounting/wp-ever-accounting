/**
 * WordPress dependencies
 */
import { Button } from '@wordpress/components';

export default function CurrencySetup( { goToNextStep, ...props } ) {
	return (
		<>
			CurrencySetup
			<Button onClick={ goToNextStep }> Go to Next</Button>
		</>
	);
}
