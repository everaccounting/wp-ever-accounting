/**
 * WordPress dependencies
 */
import { SlotFillProvider, Popover, Slot } from '@wordpress/components';
/**
 * External dependencies
 */
import { useNavigate, useLocation, useParams } from 'react-router-dom';
import { getHistory } from '@eac/navigation';

/**
 * Internal dependencies
 */
import { useMenuFix } from './hooks';
import { Routes } from './routes';
import { Header, Footer, Main } from './layout';

export function App() {
	const navigate = useNavigate();
	const location = useLocation();
	const params = useParams();
	const history = getHistory();
	history.navigate = navigate;
	history.location = location;
	history.params = params;
	useMenuFix();
	return (
		<div className="eac-layout">
			<SlotFillProvider>
				<Slot name="app-header" />
				<Header />
				<Main>
					<Routes />
				</Main>
				<Footer />
				<Slot name="app-footer" />
				<Popover.Slot />
			</SlotFillProvider>
		</div>
	);
}

export default App;
