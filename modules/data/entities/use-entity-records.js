/**
 * WordPress dependencies
 */
import { useSelect, useDispatch } from '@wordpress/data';
/**
 * Internal dependencies
 */
import { STORE_NAME } from '../constants';

/**
 *
 * @param {string} entityName Name of the entity
 * @param {Object} args argument
 * @param {Object} args.query query argument
 * @param {number} args.recordId Single id
 */
// eslint-disable-next-line no-unused-vars
export function useEntityRecords( entityName, args = {} ) {
	const { query, recordId } = args;
	const {
		entityConfig,
		// entityRecords,
		// totalEntityRecords,
		// isRequestingEntityRecords,
		// entityRecord,
		// isRequestingEntityRecord,
		// isSavingEntityRecord,
		// isDeletingEntityRecord,
		// getLastEntitySaveError,
		// getEntityRecordError,
		// getEntityRecordsError,
	} = useSelect(
		( select ) => {
			const {
				getEntity,
				// getEntityRecords,
				// getTotalEntityRecords,
				// getEntityRecordsError,
				// isResolving,
				// getEntityRecord,
				// isResolving,
				// isSavingEntityRecord,
				// isDeletingEntityRecord,
				// getLastEntitySaveError,
				// getEntityRecordError,
			} = select( STORE_NAME );
			return {
				entityConfig: getEntity( entityName ),
				// entityRecords:
				// 	entityName && getEntityRecords( entityName, query ),
				// totalEntityRecords:
				// 	entityName && getTotalEntityRecords( entityName, query ),
				// isRequestingEntityRecords:
				// 	!! entityName &&
				// 	!! isResolving( 'getEntityRecords', [ entityName, query ] ),
				// entityRecord:
				// 	entityName &&
				// 	recordId &&
				// 	getEntityRecord( entityName, recordId, query ),
				// isRequestingEntityRecord:
				// 	entityName &&
				// 	recordId &&
				// 	!! isResolving( 'getEntityRecord', [
				// 		entityName,
				// 		recordId,
				// 		query,
				// 	] ),
				// isSavingEntityRecord,
				// isDeletingEntityRecord,
				// getLastEntitySaveError,
				// getEntityRecordError: getEntityRecordError(
				// 	entityName,
				// 	recordId,
				// 	query
				// ),
				// getEntityRecordsError: getEntityRecordsError(
				// 	entityName,
				// 	query
				// ),
			};
		},
		[ entityName, query ]
	);
	const result = [ entityConfig ];
	return result;
}

export function useActions( actions = [] ) {
	const dispatch = useDispatch( STORE_NAME );
	const actionsWithDispatch = [];
	actions.forEach( ( action ) => {
		actionsWithDispatch.push( ( ...args ) => () =>
			dispatch( action( ...args ) )
		);
	} );
	return actionsWithDispatch;
}
