/**
 * WordPress dependencies
 */
import { useSelect } from '@wordpress/data';
import { useMemo } from '@wordpress/element';

/**
 * Internal dependencies
 */
import { useQuerySelect } from '../utils';
import { STORE_NAME } from './constants';

export default function useUser( userId = null ) {
	// const userData = useSelect( ( select ) => {
	// 	const { getCurrentUser, hasStartedResolution, hasFinishedResolution } = select( STORE_NAME );
	// 	return {
	// 		isRequesting: hasStartedResolution( 'getCurrentUser' ) && ! hasFinishedResolution( 'getCurrentUser' ),
	// 		user: getCurrentUser(),
	// 		getCurrentUser,
	// 	};
	// } );
	// const {currentUser} = useSelect( ( select ) => {
	// 	return {
	// 		currentUser: select( 'core' ).getCurrentUser(),
	// 	};
	// } );

	// const mutations = useMemo(
	// 	() => ( {
	// 		userCan: ( capability ) => {
	// 			return userData?.user?.capabilities?.includes( capability );
	// 		},
	// 	} ),
	// 	[ userData ]
	// );

	const user = useSelect(
		( select ) => {
			return select( 'core' ).getEntityRecord( 'root', 'user', 1 );
		},
		[ userId ]
	);

	// console.log(currentUser);

	return {
		// ...userData,
		// ...mutations,
		// isRequesting: userData.isRequesting,
		user: 'test',
	};
}
