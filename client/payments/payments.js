/**
 * External dependencies
 */
import { getTableQuery, updateQueryString } from '@eaccounting/navigation';
import { Date, ListTable, Amount } from '@eaccounting/components';

/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { useSelect, useDispatch } from '@wordpress/data';
import { getItems, CORE_STORE_NAME } from '@eaccounting/data';
/**
 * Internal dependencies
 */

/**
 * Internal dependencies
 */

// eslint-disable-next-line no-unused-vars
import Panel from './panel';
export default function Payments( props ) {
	const query = getTableQuery();
	const { items, total, isRequesting } = useSelect( ( select ) =>
		getItems( { select, name: 'payments', query } )
	);
	const { deleteEntityRecord } = useDispatch( CORE_STORE_NAME );
	return (
		<>
			<h2>Payments</h2>
			<Panel />
			<ListTable
				query={ query }
				rows={ items }
				total={ total }
				isRequesting={ isRequesting }
				onQueryChange={ updateQueryString }
				columns={ [
					{
						type: 'selection',
						property: 'id',
					},
					{
						label: __( 'Date' ),
						property: 'payment_date',
						sortable: true,
						isPrimary: true,
						render: ( row ) => {
							const { payment_date } = row;
							return <Date date={ payment_date } />;
						},
					},
					{
						label: __( 'Amount' ),
						property: 'amount',
						sortable: true,
						// render: Amount,
					},
					{
						label: __( 'Account' ),
						property: 'account_id',
						sortable: true,
						render: ( row ) => {
							const { account } = row;
							return (
								<>
									{ account && account.name && (
										<>{ account.name }</>
									) }
								</>
							);
						},
					},
					{
						label: __( 'Category' ),
						property: 'category_id',
						sortable: true,
						render: ( row ) => {
							const { category } = row;
							return (
								<>
									{ category && category.name && (
										<>{ category.name }</>
									) }
								</>
							);
						},
					},
					{
						label: __( 'Vendor' ),
						property: 'vendor_id',
						sortable: true,
						render: ( row ) => {
							const { vendor } = row;
							return (
								<>
									{ vendor && vendor.name && (
										<>{ vendor.name }</>
									) }
								</>
							);
						},
					},
					{
						label: __( 'Reference' ),
						property: 'reference',
						sortable: true,
					},
				] }
				bulkActions={ [
					{
						label: __( 'Delete' ),
						value: __( 'delete' ),
					},
				] }
				onBulkAction={ ( action, selected ) => {
					console.log( action, selected );
					if ( action === 'delete' ) {
						selected.map( ( payment ) =>
							deleteEntityRecord( 'payments', payment.id )
						);
					}
				} }
			/>
		</>
	);
}
