/**
 * WordPress dependencies
 */
import { useSelect, useDispatch } from '@wordpress/data';
import { useCallback } from '@wordpress/element';
/**
 * Internal dependencies
 */
import { STORE_NAME } from '../constants';

/**
 * Entity store wrapper.
 *
 * @param {string} entityName Name of the entity
 * @param {Object} args extra argument.
 * @param {Object} args.query Query.
 * @param {number} args.recordId Single record id.
 * @return {{isSavingEntityRecord, getLastEntitySaveError, entityRecords: *, isRequestingEntityRecords: *, saveEntityRecord, updateEntityRecord, entityRecord: *, deleteEntityRecord, isDeletingEntityRecord, isRequestingEntityRecord: *, entityRecordError: *, entity: *, entityRecordsFetchError: *}} Entities
 */
export const useEntities = ( entityName, args = {} ) => {
	const { query, recordId } = args;
	const id = parseInt( recordId, 10 );

	const {
		entity,
		entityRecords,
		isRequestingEntityRecords,
		entityRecordsFetchError,
		isSavingEntityRecord,
		isDeletingEntityRecord,
		getLastEntitySaveError,
	} = useSelect(
		( select ) => {
			const {
				getEntity,
				getEntityRecords,
				getEntityRecordsError,
				isResolving,
				isSavingEntityRecord,
				isDeletingEntityRecord,
				getLastEntitySaveError,
			} = select( STORE_NAME );
			return {
				entity: getEntity( entityName ),
				entityRecords: getEntityRecords( entityName, query ),
				isRequestingEntityRecords: isResolving( 'getEntityRecords', [
					entityName,
					query,
				] ),
				entityRecordsFetchError: getEntityRecordsError(
					entityName,
					query
				),
				isSavingEntityRecord,
				isDeletingEntityRecord,
				getLastEntitySaveError,
			};
		},
		[ entityName, query ]
	);

	const {
		entityRecord,
		isRequestingEntityRecord,
		entityRecordFetchError,
	} = useSelect(
		( select ) => {
			if ( ! id ) {
				return {};
			}

			const {
				getEntityRecord,
				getEntityRecordError,
				isResolving,
			} = select( STORE_NAME );

			return {
				entityRecord: getEntityRecord( entityName, id, query ),
				isRequestingEntityRecord: isResolving( 'getEntityRecord', [
					entityName,
					query,
					id,
				] ),
				entityRecordFetchError: getEntityRecordError(
					entityName,
					id,
					query
				),
			};
		},
		[ entityName, query, id ]
	);

	const { saveEntityRecord, deleteEntityRecord } = useDispatch( STORE_NAME );

	return {
		entity,
		entityRecords,
		isRequestingEntityRecords,
		entityRecordsFetchError,
		entityRecord,
		isRequestingEntityRecord,
		entityRecordFetchError,
		isRequesting:
			( isRequestingEntityRecords || isRequestingEntityRecord ) === true,
		saveEntityRecord: useCallback(
			( edits, customHandler = null ) =>
				saveEntityRecord( entityName, edits, customHandler ),
			[ entityName, saveEntityRecord ]
		),
		updateEntityRecord: useCallback(
			( edits, customHandler = null ) =>
				saveEntityRecord( entityName, edits, customHandler ),
			[ entityName, saveEntityRecord ]
		),
		isSavingEntityRecord: useCallback(
			( recordId ) => isSavingEntityRecord( entityName, recordId ),
			[ entityName, isSavingEntityRecord ]
		),
		deleteEntityRecord: useCallback(
			( recordId ) => deleteEntityRecord( entityName, recordId ),
			[ deleteEntityRecord, entityName ]
		),
		isDeletingEntityRecord: useCallback(
			( recordId ) => isDeletingEntityRecord( entityName, recordId ),
			[ entityName, isDeletingEntityRecord ]
		),
		getLastEntitySaveError: useCallback(
			( recordId ) => getLastEntitySaveError( entityName, recordId ),
			[ entityName, getLastEntitySaveError ]
		),
	};
};
