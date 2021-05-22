// export default function use
/**
 * WordPress dependencies
 */
import { useSelect, useDispatch } from '@wordpress/data';
import { useCallback } from '@wordpress/element';
/**
 * Internal dependencies
 */
import { STORE_KEY } from './constants';

export function useEntity({name, query = {}, id = null }) {
	console.log(name);
	const { entities, schema, entity, total, isLoading, getLastEntitySaveError } = useSelect(
		(select) => {
			const { getEntities, getEntity, getSchema, getTotal, isRequesting, getLastEntitySaveError } = select(STORE_KEY);

			return {
				entities: name && getEntities(name, query),
				schema: name && getSchema(name ),
				entity: id && getEntity(name, id, query),
				total: name && getTotal(name, query),
				getLastEntitySaveError,
				isLoading: name && isRequesting('getEntities', name, query),
			};
		},
		[name, query, id]
	);

	const { saveEntity : saveItem, deleteEntity:deleteItem} =useDispatch(STORE_KEY)

	const saveEntity = useCallback( (edits, customHandler = null) => saveItem( name, edits, customHandler ), [
		name
	] );

	const deleteEntity = useCallback( (id) => deleteItem( name, id ), [
		name
	] );

	const onSaveError = useCallback( (id) => getLastEntitySaveError( name, id ), [
		name, id
	] );

	return { entities, total, isLoading, entity, schema, saveEntity, deleteEntity, onSaveError };
}
