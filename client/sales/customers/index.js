/**
 * WordPress dependencies
 */
import { __, _x } from '@wordpress/i18n';
import { Button, ToggleControl, Notice, Spacer } from '@wordpress/components';
import { withSelect, withDispatch } from '@wordpress/data';
import { compose } from '@wordpress/compose';
import { useState } from '@wordpress/element';
/**
 * External dependencies
 */
import { get, isEmpty } from 'lodash';
import {
	Text,
	H,
	ListTable,
	Amount,
	Gravatar,
	CustomerModal,
} from '@eaccounting/components';
import {
	getTableQuery,
	updateQueryString,
	getActiveFiltersFromQuery,
} from '@eaccounting/navigation';

const entityName = 'customers';

const filters = {};

function Customers( props ) {
	const {
		query,
		items,
		total,
		isRequesting,
		fetchError,
		defaultCurrency,
		saveEntityRecord,
		deleteEntityRecord,
		isSavingEntityRecord,
	} = props;
	const { search } = query;
	const [ editingItem, setEditingItem ] = useState( false );
	return (
		<>
			<H className="wp-heading-inline">{ __( 'Customers' ) }</H>
			<Button
				className="page-title-action"
				isSecondary
				onClick={ () => setEditingItem( {} ) }
			>
				{ __( 'Add Customer' ) }
			</Button>

			{ editingItem && (
				<CustomerModal
					item={ editingItem }
					onClose={ () => setEditingItem( false ) }
					onSave={ () => setEditingItem( false ) }
				/>
			) }

			{ ! isEmpty( fetchError ) && (
				<>
					<Notice isDismissible={ false } status="error">
						<Text>{ fetchError.message }</Text>
					</Notice>
					<Spacer marginBottom={ 20 } />
				</>
			) }

			<ListTable
				query={ query }
				isRequesting={ isRequesting }
				rows={ items }
				total={ total }
				onQueryChange={ ( query ) =>
					updateQueryString( query, '/banking', {} )
				}
				bulkActions={ [
					{
						label: __( 'Delete' ),
						value: __( 'delete' ),
					},
					{
						label: __( 'Enable' ),
						value: __( 'enable' ),
					},
					{
						label: __( 'Disable' ),
						value: __( 'disable' ),
					},
					{
						label: __( 'Export' ),
						value: __( 'export' ),
					},
				] }
				onBulkAction={ ( action, selected ) => {
					console.log( action, selected );
				} }
				filters={ filters }
				columns={ [
					{
						type: 'selection',
						property: 'id',
					},
					{
						property: '',
						width: 50,
						render: ( row ) => {
							return <Gravatar user={ row.email } size={ 36 } />;
						},
					},
					{
						label: __( 'Name' ),
						property: 'name',
						isPrimary: true,
						sortable: true,
						render: ( row ) => {
							const highlightWords = !! search ? [ search ] : '';
							return (
								<Text
									color="var(--wp-admin-theme-color)"
									style={ {
										cursor: 'pointer',
										fontWeight: '600',
									} }
									highlightWords={ highlightWords }
									onClick={ () => setEditingItem( row ) }
								>
									{ row.name }
								</Text>
							);
						},
						actions: [
							{
								label: __( 'Edit' ),
								onClick: ( row ) => setEditingItem( row ),
							},
							{
								label: __( 'Delete' ),
								onClick: ( row ) => {
									if (
										window.confirm(
											__(
												'Do you really want to delete the item?'
											)
										)
									) {
										deleteEntityRecord( row.id );
									}
								},
							},
						],
					},
					{
						label: __( 'Contact' ),
						property: 'contact',
						sortable: false,
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
							const { total_paid, currency } = row;
							return (
								<Amount
									amount={ total_paid }
									currency={ currency }
								/>
							);
						},
					},
					{
						label: __( 'Receivable' ),
						property: 'total_due',
						render: ( row ) => {
							const { total_due, currency } = row;
							return (
								<Amount
									amount={ total_due }
									currency={ currency }
								/>
							);
						},
					},
					{
						label: __( 'Enabled' ),
						property: 'status',
						sortable: true,
						width: 150,
						render: ( row ) => {
							return (
								<ToggleControl
									disabled={ isSavingEntityRecord( row.id ) }
									checked={ row.enabled }
									onChange={ ( enabled ) =>
										saveEntityRecord( {
											id: row.id,
											enabled,
										} )
									}
								/>
							);
						},
					},
				] }
			/>
		</>
	);
}

const applyWithSelect = withSelect( ( select, props ) => {
	const { tab } = props.query;
	const tableQuery = getTableQuery( [
		...Object.keys( getActiveFiltersFromQuery( filters, props.query ) ),
	] );
	const {
		getEntityRecords,
		getTotalEntityRecords,
		getEntityFetchError,
		isResolving,
		getDefaultCurrency,
		isSavingEntityRecord,
	} = select( 'ea/core' );
	return {
		items: getEntityRecords( entityName, tableQuery ),
		total: getTotalEntityRecords( entityName, tableQuery ),
		isRequesting: isResolving( 'getEntityRecords', [
			entityName,
			tableQuery,
		] ),
		fetchError: getEntityFetchError( entityName, tableQuery ),
		isSavingEntityRecord: isSavingEntityRecord( entityName ),
		defaultCurrency: getDefaultCurrency(),
		tab,
	};
} );

const applyWithDispatch = withDispatch( ( dispatch ) => {
	const { deleteEntityRecord, saveEntityRecord } = dispatch( 'ea/core' );
	return {
		deleteEntityRecord: ( id ) => deleteEntityRecord( entityName, id ),
		saveEntityRecord: ( item ) => saveEntityRecord( entityName, item ),
	};
} );

export default compose( [ applyWithSelect, applyWithDispatch ] )( Customers );
