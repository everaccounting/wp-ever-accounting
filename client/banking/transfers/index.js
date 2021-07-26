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
	ItemModal,
} from '@eaccounting/components';
import {
	getTableQuery,
	updateQueryString,
	getActiveFiltersFromQuery,
} from '@eaccounting/navigation';

const entityName = 'transfers';

const filters = {
	status: {
		title: __( 'Status' ),
		mixedString: '{{title}}Status{{/title}} {{rule /}} {{filter /}}',
		input: {
			component: 'SelectFilter',
			options: [
				{ label: '== Select ==', value: '' },
				{ label: __( 'Enabled' ), value: 'enabled' },
				{ label: __( 'Disabled' ), value: 'disabled' },
			],
		},
	},
	category: {
		title: __( 'Category' ),
		mixedString: '{{title}}Category{{/title}} {{rule /}} {{filter /}}',
		input: {
			component: 'EntityFilter',
			entityName: 'itemCategories',
			isMulti: true,
		},
		rules: [
			{
				value: '_in',
				label: __( 'In' ),
			},
			{
				value: '_not_in',
				label: __( 'No In' ),
			},
		],
	},
	sale_price: {
		title: __( 'Sale Price' ),
		mixedString: '{{title}}Sale Price{{/title}} {{rule /}} {{filter /}}',
		input: {
			component: 'NumberFilter',
		},
		rules: [
			{
				value: 'max',
				label: __( 'Less Than' ),
			},
			{
				value: 'min',
				label: __( 'More Than' ),
			},
			{
				value: 'between',
				label: __( 'Between' ),
			},
		],
	},
	purchase_price: {
		title: __( 'Purchase Price' ),
		mixedString:
			'{{title}}Purchase Price{{/title}} {{rule /}} {{filter /}}',
		input: {
			component: 'NumberFilter',
		},
		rules: [
			{
				value: 'max',
				label: __( 'Less Than' ),
			},
			{
				value: 'min',
				label: __( 'More Than' ),
			},
			{
				value: 'between',
				label: __( 'Between' ),
			},
		],
	},
};

function Transfers( props ) {
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
			<H className="wp-heading-inline">{ __( 'Transfers' ) }</H>
			<Button
				className="page-title-action"
				isSecondary
				onClick={ () => setEditingItem( {} ) }
			>
				{ __( 'Add Transfer' ) }
			</Button>

			{ editingItem && (
				<ItemModal
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
						render: () => {
							return <Gravatar size={ 36 } />;
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
						label: __( 'Sale price' ),
						property: 'sale_price',
						sortable: true,
						render: ( row ) => {
							const { sale_price } = row;
							return (
								<Amount
									amount={ sale_price }
									currency={ defaultCurrency }
								/>
							);
						},
					},
					{
						label: __( 'Purchase price' ),
						property: 'purchase_price',
						sortable: true,
						render: ( row ) => {
							const { purchase_price } = row;
							return (
								<Amount
									amount={ purchase_price }
									currency={ defaultCurrency }
								/>
							);
						},
					},
					{
						label: __( 'Category' ),
						property: 'category_id',
						sortable: true,
						render: ( row ) => {
							return (
								<Text>
									{ get( row, [ 'category', 'name' ], '-' ) }
								</Text>
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

export default compose( [ applyWithSelect, applyWithDispatch ] )( Transfers );
