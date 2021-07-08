/**
 * WordPress dependencies
 */
import { useSelect } from '@wordpress/data';
/**
 * External dependencies
 */
import { STORE_NAME } from '@eaccounting/data';

export const getEntityRecords = ( entityName, query = {} ) => {
	const result = useSelect( ( select ) => {
		const { getEntityRecords, getTotalEntityRecords, isResolving } = select(
			STORE_NAME
		);

		const items = getEntityRecords( entityName, query );
		const total = getTotalEntityRecords( entityName, query );
		const isRequesting = isResolving( 'getEntityRecords', [
			entityName,
			query,
		] );
		return {
			items,
			total,
			isRequesting,
		};
	} );

	return { ...result };
};
