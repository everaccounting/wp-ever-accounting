/**
 * External dependencies
 */
import { Outlet } from 'react-router-dom';
import PageNavigation from '~/components/page-navigation';

function Items( props ) {
	const { routes } = props;
	return (
		<>
			<PageNavigation routes={ routes } />
			<Outlet />
		</>
	);
}

export default Items;
