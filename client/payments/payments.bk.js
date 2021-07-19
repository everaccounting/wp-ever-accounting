/**
 * External dependencies
 */
import { getQuery, updateQueryString } from '@eaccounting/navigation';
import { Date, ListTable } from '@eaccounting/components';

/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { useSelect, useDispatch } from '@wordpress/data';
import { STORE_NAME } from '@eaccounting/data';
/**
 * Internal dependencies
 */

// eslint-disable-next-line no-unused-vars
export default function Payments( props ) {
	// const entityName = 'payments';
	// const query = getQuery();
	// const { items, total, isRequesting } = useSelect( ( select ) => {
	// 	const { getEntityRecords, getTotalEntityRecords, isResolving } = select(
	// 		STORE_NAME
	// 	);
	//
	// 	const items = getEntityRecords( entityName, query );
	// 	const total = getTotalEntityRecords( entityName, query );
	// 	const isRequesting = isResolving( 'getEntityRecords', [
	// 		entityName,
	// 		query,
	// 	] );
	// 	return {
	// 		items,
	// 		total,
	// 		isRequesting,
	// 	};
	// } );
	//
	// const { deleteEntityRecord } = useDispatch( STORE_NAME );

	return (
		<>
			<h2>Payments</h2>
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
				filters={ {
					category_id: {
						title: __( 'Category' ),
						input: {
							component: 'Search',
							entityName: 'categories',
							isMulti: true,
						},
					},
					account_id: {
						title: __( 'Account' ),
						input: {
							component: 'Search',
							entityName: 'accounts',
						},
					},
					payment_date: {
						title: __( 'Payment Date' ),
						rules: [
							{
								value: 'before',
								label: __( 'Before' ),
							},
							{
								value: 'after',
								label: __( 'After' ),
							},
							{
								value: 'between',
								label: __( 'Between' ),
							},
						],
						input: {
							component: 'Date',
						},
					},
				} }
			/>
		</>
	);
}
