// export default function use
import { useSelect, useDispatch } from '@wordpress/data';
import { useCallback } from '@wordpress/element';
import {STORE_KEY} from "./constants";

export function useEntities(name, query = {} ) {

	const { entities, total, isLoading } = useSelect(
		( select ) => {
			const { getEntities, getTotal, isRequesting } = select( STORE_KEY );

			return {
				entities:getEntities(name, query),
				total:getTotal(name, query),
				isLoading:isRequesting(
					'getEntities',
					name,
					query
				),
			}
		},
		[ name, query ]
	);
	const { saveEntity } = useDispatch( STORE_KEY );

	const updateEntity = useCallback(
		( edits ) => {
			saveEntity( name, edits);
		},
		[ name ]
	);

	return {entities, total, isLoading, updateEntity} ;
}
