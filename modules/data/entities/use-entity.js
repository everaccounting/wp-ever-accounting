// export default function use
/**
 * WordPress dependencies
 */
import {useSelect, useDispatch} from '@wordpress/data';
import {useCallback} from '@wordpress/element';
/**
 * Internal dependencies
 */
import {STORE_NAME} from './constants';


export function useEntity({name, query = {}, id = null}) {
	const {entities, total, entity, getLastEntitySaveError, isRequestingEntityRecords, isRequestingEntityRecord} = useSelect(
		(select) => {
			const {
				getEntityRecords,
				getEntityRecord,
				getTotalEntityRecords,
				getLastEntitySaveError,
				isRequestingEntityRecords
			} = select(STORE_NAME)
			return {
				entities: getEntityRecords(name, query),
				total: getTotalEntityRecords(name, query),
				entity: getEntityRecord(name, query, id),
				getLastEntitySaveError,
				isRequestingEntityRecords: isRequestingEntityRecords( name, query),
				isRequestingEntityRecord: isRequestingEntityRecord( name, query, id),
			}
		}, [name, query, id]);

	const {saveEntityRecord: saveItem, deleteEntityRecord: deleteItem} = useDispatch(STORE_NAME)

	const saveEntity = useCallback((edits, customHandler = null) => saveItem(name, edits, customHandler), [
		name
	]);

	const deleteEntity = useCallback((id) => deleteItem(name, id), [
		name
	]);

	const onSaveError = useCallback((id) => getLastEntitySaveError(name, id), [
		name, id
	]);

	return {entities, total, isRequestingEntityRecords, isRequestingEntityRecord, entity, saveEntity, deleteEntity, onSaveError};
}
