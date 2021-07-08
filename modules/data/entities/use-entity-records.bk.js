/**
 * WordPress dependencies
 */
import { useSelect } from '@wordpress/data';
/**
 * Internal dependencies
 */
import { STORE_NAME } from '../constants';

/**
 *
 * @param {string} entityName Name of the entity
 * @param {Object} query query argument
 * @param {string|number} recordID Record id
 */
// eslint-disable-next-line no-unused-vars
export function useEntityRecords( entityName, query = {}, recordID ) {
	const {
		entityRecords,
		totalEntityRecords,
		isRequestingEntityRecords,
		isEntityRecordsError,
		getEntityRecordsError,
	} = useSelect( ( select ) => {
		const {
			getEntityRecords,
			getTotalEntityRecords,
			getEntityRecordsError,
			isResolving,
		} = select( STORE_NAME );
		return {
			entityRecords: getEntityRecords( entityName, query ),
			totalEntityRecords: getTotalEntityRecords( entityName, query ),
			isRequestingEntityRecords:
				isResolving( 'getEntityRecords', [ entityName, query ] ) ===
				true,
			isEntityRecordsError:
				getEntityRecordsError( entityName, query ) !== false,
			getEntityRecordsError: getEntityRecordsError( entityName, query ),
		};
	} );

	return {
		entityRecords,
		totalEntityRecords,
		isRequestingEntityRecords,
		isEntityRecordsError,
		getEntityRecordsError,
	};
}
