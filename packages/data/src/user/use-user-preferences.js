/**
 * WordPress dependencies
 */
import { useDispatch, useSelect } from '@wordpress/data';
import { useMemo } from '@wordpress/element';

/**
 * External dependencies
 */
import { mapValues } from 'lodash';

/**
 * Internal dependencies
 */
import { STORE_NAME } from './constants';

export default function useUserPreferences() {
	const { isResolving, saveEntityRecord } = useDispatch( STORE_NAME );
	const { currentUser } = useSelect( ( select ) => {
		const { getCurrentUser, getLastEntitySaveError, isResolvingCurrentUser } = select( STORE_NAME );
		return {
			currentUser: getCurrentUser(),
			lastEntitySaveError: getLastEntitySaveError(),
			isResolving: isResolvingCurrentUser(),
		};
	} );

	const updateUserPreferences = async ( data ) => {
		const metaData = mapValues( data, JSON.stringify );
		if ( Object.keys( metaData ).length === 0 ) {
			return {
				error: new Error( 'Invalid data for update.' ),
				updatedUser: undefined,
			};
		}

		const updatedUser = await saveEntityRecord( 'user', currentUser.id, {
			ever_accounting_meta: metaData,
		} );

		return {
			updatedUser,
			ever_accounting_meta: getUserPreferences(),
		};
	};

	const getUserPreferences = async () => {
		const meta = currentUser.ever_accounting_meta || {};
		return mapValues( meta, ( data, key ) => {
			if ( ! data || data.length === 0 ) {
				return '';
			}
			try {
				return JSON.parse( data );
			} catch ( e ) {
				if ( e instanceof Error ) {
					/* eslint-disable no-console */
					console.error( `Error parsing value '${ data }' for ${ key }`, e.message );
					/* eslint-enable no-console */
				} else {
					/* eslint-disable no-console */
					console.error( `Unexpected Error parsing value '${ data }' for ${ key } ${ e }` );
					/* eslint-enable no-console */
				}
				return '';
			}
		} );
	};

	return {
		updateUserPreferences,
		getUserPreferences,
		isRequesting: isResolving,
	};
}
