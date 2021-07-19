/**
 * WordPress dependencies
 */
import { useDispatch, useSelect } from '@wordpress/data';
import { Button, ToggleControl } from '@wordpress/components';
/**
 * External dependencies
 */
import { getSelectors, CORE_STORE_NAME } from '@eaccounting/data';
import {
	ListTable,
	Amount,
	Gravatar,
	CustomerModal,
} from '@eaccounting/components';
import { getTableQuery, updateQueryString } from '@eaccounting/navigation';
import { __ } from '@wordpress/i18n';
import { useState } from '@wordpress/element';

// eslint-disable-next-line no-unused-vars
export default function Customers( props ) {
	const [ editingCustomer, setEditingCustomer ] = useState( false );
	const query = getTableQuery();
	const {
		items,
		total,
		isRequestingItems,
		defaultCurrency,
	} = useSelect( ( select ) =>
		getSelectors( { select, name: 'customers', query } )
	);
	const { deleteEntityRecord, saveEntityRecord } = useDispatch(
		CORE_STORE_NAME
	);

	return (
		<>
			<h1 className="wp-heading-inline">{ __( 'Customers' ) }</h1>
			<Button
				isSmall
				onClick={ () => setEditingCustomer( {} ) }
				className="page-title-action"
			>
				{ __( 'Add New' ) }
			</Button>
			<Button
				isSmall
				className="page-title-action"
				href="/wp-admin/admin.php?page=ea-tools"
			>
				{ __( 'Import' ) }
			</Button>
			{ editingCustomer && (
				<CustomerModal
					item={ editingCustomer }
					onClose={ () => setEditingCustomer( false ) }
					onSave={ () => setEditingCustomer( false ) }
				/>
			) }
			<ListTable
				query={ query }
				isRequesting={ isRequestingItems }
				rows={ items }
				total={ total }
				onQueryChange={ updateQueryString }
				columns={ [
					{
						type: 'selection',
						property: 'id',
					},
					{
						property: '',
						width: 60,
						render: ( row ) => {
							return <Gravatar user={ row.email } size={ 36 } />;
						},
					},
					{
						label: __( 'Name' ),
						property: 'name',
						sortable: true,
						isPrimary: true,
						render: ( row ) => {
							return (
								<>
									{ row.name }
									<br />
									<span
										className="meta"
										style={ {
											display: 'block',
											color: '#999',
										} }
									>
										{ row.company ? row.company : '-' }
									</span>
								</>
							);
						},
						actions: [
							{
								label: __( 'Edit' ),
								onClick: ( customer ) => {
									setEditingCustomer( customer );
								},
							},
							{
								label: __( 'Delete' ),
								onClick: ( customer ) => {
									deleteEntityRecord(
										'customers',
										customer.id
									);
								},
							},
						],
					},
					{
						label: __( 'Contact' ),
						property: 'contact',
						render: ( row ) => {
							const { email, phone } = row;
							return (
								<>
									{ email && (
										<>
											<a href={ `mailto:${ email }` }>
												{ email }
											</a>
											<br />
										</>
									) }
									{ phone }
								</>
							);
						},
					},
					{
						label: __( 'Paid' ),
						property: 'paid',
						render: ( row ) => {
							const { total_paid } = row;
							return (
								<Amount
									amount={ total_paid }
									currency_code={ defaultCurrency.code }
								/>
							);
						},
					},
					{
						label: __( 'Receivable' ),
						property: 'total_due',
						render: ( row ) => {
							const { total_due } = row;
							return (
								<Amount
									amount={ total_due }
									currency_code={ defaultCurrency.code }
								/>
							);
						},
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
										saveEntityRecord( 'customers', {
											id: row.id,
											enabled,
										} )
									}
								/>
							);
						},
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
						selected.map( ( customer ) =>
							deleteEntityRecord( 'customers', customer.id )
						);
					}
				} }
			/>
		</>
	);
}
