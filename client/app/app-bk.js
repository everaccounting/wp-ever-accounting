import { lazy, Suspense } from 'react';
import { getHistory, getQuery } from '@eac/navigation';
import {
	unstable_HistoryRouter as HistoryRouter,
	Route,
	Routes,
	useLocation,
	useMatch,
	useParams,
} from 'react-router-dom';
const Dashboard = lazy(() => import('./pages/dashboard'));
export function App() {
	const path = document.location.pathname;
	const basename = path.substring(0, path.lastIndexOf('/'));
	return (
		<div>
			<HistoryRouter history={getHistory()}>
				<Routes basename={basename}>
					<Route path="/dashboard" element={<Dashboard />} />
					<Route path="/" element={<Dashboard />} />
				</Routes>
			</HistoryRouter>
		</div>
	);
}

export default App;
