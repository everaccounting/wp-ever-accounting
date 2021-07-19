/**
 * WordPress dependencies
 */
import { useSelect, useDispatch } from '@wordpress/data';
import { __ } from '@wordpress/i18n';
import { ToggleControl } from '@wordpress/components';
/**
 * Internal dependencies
 */
/**
 * External dependencies
 */
import { getItems, CORE_STORE_NAME } from '@eaccounting/data';
import {
	getTableQuery,
	updateQueryString,
	onQueryChange,
} from '@eaccounting/navigation';
import { Table } from '@eaccounting/components';

export default function Accounts() {
	const query = getTableQuery();
	const { items, isRequesting } = useSelect( ( select ) =>
		getItems( { select, name: 'accounts', query } )
	);
	const { saveEntityRecord } = useDispatch( CORE_STORE_NAME );
	return (
		<>
			<h1>Accounts</h1>
			<Table
				columns={ [
					{
						label: __( 'Account Name' ),
						property: 'name',
						sortable: true,
						actions: [
							{
								label: 'New',
								onClick: ( row ) => {
									updateQueryString( {
										id: row.id,
										action: 'edit',
									} );
								},
							},
							{
								label: 'edit',
								onClick: ( row ) => {
									updateQueryString( { id: row.id } );
								},
							},
						],
					},
					{
						label: __( 'Balance' ),
						property: 'balance',
						sortable: true,
					},
					{
						label: __( 'Bank Name' ),
						property: 'bank_name',
						sortable: true,
					},
					{
						label: __( 'Enabled' ),
						property: 'enabled',
						sortable: true,
						width: 150,
						render: ( row ) => {
							return (
								<ToggleControl
									checked={ row.enabled }
									onChange={ ( enabled ) =>
										saveEntityRecord( 'accounts', {
											id: row.id,
											enabled,
										} )
									}
								/>
							);
						},
					},
				] }
				isRequesting={ isRequesting }
				rows={ items }
				onSort={ ( sort ) => onQueryChange( 'sort' )( sort ) }
			/>
		</>
	);
}
