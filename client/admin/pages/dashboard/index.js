/**
 * External dependencies
 */
import { SectionHeader, Input, Text } from '@eac/components';

/**
 * WordPress dependencies
 */
import { useState } from '@wordpress/element';
import { useSelect } from '@wordpress/data';
import { Button } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

/**
 * Internal dependencies
 */
import ProTable from './table';
import { useSearchParams } from 'react-router-dom';

function Dashboard( props ) {
	const [ searchParams, setSearchParams ] = useSearchParams();
	const query = Object.fromEntries( searchParams.entries() );
	const items = useSelect(
		( select ) => {
			return select( 'eac/entities' ).getRecords( 'item', query );
		},
		[ query ]
	);
	return (
		<>
			<ProTable
				query={ query }
				onChange={ setSearchParams }
				headerTitle={
					<Text size={ 16 } weight={ 600 } as="h2" color="#23282d">
						{ __( 'Dashboard', 'wp-ever-accounting' ) }
					</Text>
				}
				headerActions={
					<>
						<Button isSecondary={ true } size="small">
							Add Item
						</Button>
						<Button isSecondary={ true } size="small">
							Add Item
						</Button>
					</>
				}
				options={ {
					search: {
						placeholder: 'Search Item',
						style: {
							width: '100%',
						},
					},
				} }
				columns={ [
					{
						key: 'name',
						title: 'Name',
						property: 'name',
						sortable: true,
					},
				] }
				data={ items }
			/>
		</>
	);
}

export default Dashboard;
