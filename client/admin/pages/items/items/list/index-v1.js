/**
 * External dependencies
 */
import { Table, Heading, Space } from '@eac/components';
import { useEntityRecords } from '@eac/data';
import { useNavigate, useLocation, useSearchParams, Link } from 'react-router-dom';
/**
 * WordPress dependencies
 */
import { Button } from '@wordpress/components';

/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';

function ItemsList() {
	const [ searchParams, setSearchParams ] = useSearchParams();
	const query = Object.fromEntries( searchParams.entries() );
	const items = useEntityRecords( 'item', query );
	return (
		<>
			<Table
				headerTitle={ <Heading>{ __( 'Items', 'wp-ever-accounting' ) }</Heading> }
				headerActions={
					<Space>
						<Button isPrimary={ true } icon="plus">
							Add Item
						</Button>
						<Button isSecondary={ true } icon="upload">
							Import
						</Button>
					</Space>
				}
				loading={ items.status === 'resolving' }
				query={ query }
				columns={ [
					{
						title: __( 'Name' ),
						key: 'name',
						sortable: true,
					},
					{
						title: __( 'Price' ),
						key: 'price',
						sortable: true,
					},
					{
						title: __( 'Cost' ),
						key: 'cost',
						sortable: true,
					},
					{
						title: __( 'Type' ),
						key: 'type',
						sortable: true,
					},
					{
						title: __( 'Status' ),
						key: 'status',
						sortable: true,
					},
					{
						title: __( 'Actions' ),
						key: 'actions',
						render: ( item ) => {
							return (
								<Space>
									<Link to={ `${ item.id }/edit` }>{ __( 'Edit', 'wp-ever-accounting' ) }</Link>
									<Link to={ `${ item.id }/delete` }>{ __( 'Delete', 'wp-ever-accounting' ) }</Link>
								</Space>
							);
						},
					},
				] }
				data={ items.records }
				totalCount={ items.totalCount }
				rowKey="id"
				search={ {
					placeholder: __( 'Search Items', 'wp-ever-accounting' ),
				} }
				onChange={ setSearchParams }
			/>
		</>
	);
}

export default ItemsList;
