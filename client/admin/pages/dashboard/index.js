/**
 * External dependencies
 */
import { SectionHeader, usePagination } from '@eac/components';
/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { useSearchParams } from 'react-router-dom';
import { useEntityRecords } from '@eac/data';

function Dashboard() {
	const [ searchParams, setSearchParams ] = useSearchParams();
	const query = Object.fromEntries( searchParams.entries() );
	const items = useEntityRecords( 'item', query );
	const pagination = usePagination( {
		total: items.recordsCount,
		onChange: ( page, perPage ) => {
			setSearchParams( { page, perPage } );
		},
		pagination: {
			page: query?.page || 1,
			perPage: query?.perPage || 20,
			total: items.recordsCount,
		},
	} );
	console.log(pagination)
	return (
		<>
			<SectionHeader title={ __( 'Dashboard', 'wp-ever-accounting' ) } />
		</>
	);
}

export default Dashboard;
