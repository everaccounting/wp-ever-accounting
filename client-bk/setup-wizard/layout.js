/**
 * WordPress dependencies
 */
import { Button } from '@wordpress/components';
/**
 * External dependencies
 */
import { getHistory, getNewPath } from '@eaccounting/navigation';

function Layout({ step, steps, ...props }) {
	// history.push('/company_setup');
	const handleClick = () => {
		getHistory().go(getNewPath({}, step.path));
	};

	return (
		<>
			<Button onClick={handleClick}>CLICK ME</Button>
		</>
	);
}

export default Layout;
