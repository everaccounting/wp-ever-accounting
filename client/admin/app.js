/**
 * External dependencies
 */
import { useLocation } from 'react-router-dom';
/**
 * WordPress dependencies
 */
import { Placeholder, Result } from '@eac/components';
import { Button } from '@wordpress/components';

export function App() {
	return (
		<div className="eac-layout">
			<Result title="Your operation has been executed" subTitle="Your operation has been executed" extra={ <Button isPrimary>Go to Dashboard</Button> } />
			<Result
				status="server-error"
				title="Your operation has been executed"
				subTitle="Your operation has been executed"
				extra={ <Button isPrimary>Go to Dashboard</Button> }
			/>
		</div>
	);
}

export default App;
