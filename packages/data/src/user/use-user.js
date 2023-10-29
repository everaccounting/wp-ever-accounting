/**
 * WordPress dependencies
 */
import { useSelect } from '@wordpress/data';
import { useMemo } from '@wordpress/element';

/**
 * Internal dependencies
 */
import { STORE_NAME } from './constants';

export default function useUser() {
	const userData = useSelect( ( select ) => {
		const { getCurrentUser, hasStartedResolution, hasFinishedResolution } = select( STORE_NAME );

		return {
			isRequesting: hasStartedResolution( 'getCurrentUser' ) && ! hasFinishedResolution( 'getCurrentUser' ),
			user: getCurrentUser(),
			getCurrentUser,
		};
	} );

	const mutations = useMemo(
		() => ( {
			userCan: ( capability ) => {
				const capabilities = userData?.user?.capabilities || {};
				return Object.keys( capabilities ).includes( capability );
			},
		} ),
		[ userData ]
	);

	return {
		...userData,
		...mutations,
		userData: userData.user,
		isRequesting: userData.isRequesting,
	};
}
