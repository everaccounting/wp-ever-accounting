/**
 * WordPress dependencies
 */
import { Suspense } from '@wordpress/element';

function Main( props ) {
	const { children } = props;
	return (
		<Suspense fallback={ null }>
			<div className="eac-layout__main eac-layout__wrapper">{ children }</div>
		</Suspense>
	);
}

export default Main;
