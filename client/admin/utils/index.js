/**
 * External dependencies
 */
import { useNavigate, useLocation } from 'react-router-dom';

export const useQueryParam = ( key, value ) => {
	const navigate = useNavigate();
	const location = useLocation();
	const currentPathname = location.pathname;
	const searchParams = new URLSearchParams( location.search );
	if ( searchParams.get( key ) === value ) {
		searchParams.delete( key );
	} else {
		searchParams.set( key, value );
	}

	navigate( `${ currentPathname }?${ searchParams.toString() }` );
};
