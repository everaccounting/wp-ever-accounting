/**
 * WordPress dependencies
 */
import { useDispatch, useSelect } from '@wordpress/data';
import { Button } from '@wordpress/components';
import { useState } from '@wordpress/element';
/**
 * External dependencies
 */
import { getSelectors, CORE_STORE_NAME } from '@eaccounting/data';
import { ListTable, Date, Amount, Link } from '@eaccounting/components';
import {
	getTableQuery,
	updateQueryString,
	generatePath,
} from '@eaccounting/navigation';
import { __, _x } from '@wordpress/i18n';

/**
 * Internal dependencies
 */
import Panel from './panel';
export default function Revenues( props ) {
	const query = getTableQuery();
	const { tab } = props.query;
	const { items, total, isRequestingItems } = useSelect( ( select ) =>
		getSelectors( { select, name: 'revenues', query } )
	);
	const { deleteEntityRecord } = useDispatch( CORE_STORE_NAME );
	const [ isOpen, setOpen ] = useState( false );

	return (
		<>
			{ isOpen && (
				<Panel title={ 'Title' } subtitle={ 'Subtitle' }>
					Lorem ipsum dolor sit amet, consectetur adipisicing elit.
					Atque, eveniet?
				</Panel>
			) }
			<h1>Revenues</h1>
			<Button onClick={ () => setOpen( ! isOpen ) }>Open</Button>
			<ListTable
				query={ query }
				isRequesting={ isRequestingItems }
				rows={ items }
				total={ total }
				onQueryChange={ ( query ) => {
					console.log( query );
					updateQueryString( query, '/sales', {} );
				} }
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
							return (
								<>
									<Link
										href={ generatePath(
											{ id: row.id },
											'/sales',
											{ tab }
										) }
									>
										<Date date={ payment_date } />
									</Link>
								</>
							);
						},
						actions: [
							{
								label: __( 'Delete' ),
								onClick: ( payment ) => {
									deleteEntityRecord(
										'payments',
										payment.id
									);
								},
							},
						],
					},
					{
						label: __( 'Amount' ),
						property: 'amount',
						sortable: true,
						render: ( row ) => {
							const { currency_code, amount } = row;
							return (
								<Amount
									amount={ amount }
									currency_code={ currency_code }
								/>
							);
						},
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
						label: __( 'Customer' ),
						property: 'customer_id',
						sortable: true,
						render: ( row ) => {
							const { customer } = row;
							return (
								<>
									{ customer && customer.name && (
										<>{ customer.name }</>
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
					if ( action === 'delete' ) {
						selected.map( ( payment ) =>
							deleteEntityRecord( 'payments', payment.id )
						);
					}
				} }
				filters={ {
					status: {
						title: __( 'Status' ),
						mixedString:
							'{{title}}Status{{/title}} {{rule /}} {{filter /}}',
						input: {
							component: 'SelectFilter',
							options: [
								{ label: '== Select ==', value: '' },
								{ label: 'Afghanistan', value: 'AF' },
								{ label: 'Åland Islands', value: 'AX' },
								{ label: 'Albania', value: 'AL' },
								{ label: 'Algeria', value: 'DZ' },
								{ label: 'American Samoa', value: 'AS' },
								{ label: 'AndorrA', value: 'AD' },
								{ label: 'Angola', value: 'AO' },
								{ label: 'Anguilla', value: 'AI' },
								{ label: 'Antarctica', value: 'AQ' },
								{ label: 'Antigua and Barbuda', value: 'AG' },
								{ label: 'Argentina', value: 'AR' },
								{ label: 'Armenia', value: 'AM' },
								{ label: 'Aruba', value: 'AW' },
							],
						},
						rules: [
							{
								value: 'in',
								label: __( 'In' ),
							},
							{
								value: 'not_in',
								label: __( 'No In' ),
							},
						],
						allowMultiple: true,
					},
					account: {
						title: __( 'Account' ),
						mixedString:
							'{{title}}Account{{/title}} {{rule /}} {{filter /}}',
						input: {
							component: 'EntityFilter',
							entityName: 'accounts',
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
					payment_date: {
						title: __( 'Payment Date' ),
						mixedString:
							'{{title}}Payment Date{{/title}} {{rule /}} {{filter /}}',
						input: {
							component: 'DateFilter',
						},
						rules: [
							{
								value: 'before',
								/* translators: Sentence fragment, logical, "Before" refers to customers registered before a given date. Screenshot for context: https://cloudup.com/cCsm3GeXJbE */
								label: _x( 'Before', 'date' ),
							},
							{
								value: 'after',
								/* translators: Sentence fragment, logical, "after" refers to customers registered after a given date. Screenshot for context: https://cloudup.com/cCsm3GeXJbE */
								label: _x( 'After', 'date' ),
							},
							{
								value: 'between',
								/* translators: Sentence fragment, logical, "Between" refers to average order value of a customer, between two given amounts. Screenshot for context: https://cloudup.com/cCsm3GeXJbE */
								label: _x( 'Between', 'date' ),
							},
						],
					},
					amount: {
						title: __( 'Amount' ),
						mixedString:
							'{{title}}Amount{{/title}} {{rule /}} {{filter /}}',
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
				} }
			/>
		</>
	);
}
