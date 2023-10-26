/**
 * External dependencies
 */
import { useLocation } from 'react-router-dom';
/**
 * WordPress dependencies
 */
import { Spinner } from '@eac/components';

export function App() {
	return (
		<div className="eac-layout">
			lorem ipsum
			<Spinner active fullscreen />
		</div>
	);
}

export default App;
