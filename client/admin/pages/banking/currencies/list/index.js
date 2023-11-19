/**
 * External dependencies
 */
import {
	SectionHeader,
	Spinner,
	Result,
	Table,
	Dropdown,
	Placeholder,
	Space,
} from '@eac/components';
import { useEntityRecords } from '@eac/data';
import { useQuery } from '@eac/navigation';
import { useLocation, useSearchParams } from 'react-router-dom';
/**
 * WordPress dependencies
 */
import { useLayoutEffect } from '@wordpress/element';
import { __ } from '@wordpress/i18n';

/**
 * Internal dependencies
 */

const columns = [
	{
		type: 'expandable',
	},
	{
		type: 'selectable',
	},
	{
		key: 'name',
		title: __( 'Name', 'wp-ever-accounting' ),
		sortable: true,
		tooltip: __( 'Name', 'wp-ever-accounting' ),
	},
	{
		key: 'exchange_rate',
		title: __( 'Exchange Rate', 'wp-ever-accounting' ),
		sortable: true,
	},
	{
		key: 'code',
		title: __( 'Code', 'wp-ever-accounting' ),
		sortable: true,
	},
	{
		key: 'symbol',
		title: __( 'Symbol', 'wp-ever-accounting' ),
		sortable: true,
	},
];

function CurrenciesTable() {
	const [ searchParams, setSearchParams ] = useSearchParams();
	const query = Object.fromEntries( searchParams.entries() );
	const currencies = useEntityRecords( 'currency', query );

	const tableProps = {
		query,
		columns,
		data: currencies.records,
		pagination: {
			total: currencies?.recordsCount,
			page: query?.page,
			perPage: query?.perPage,
		},
		loading: currencies.status === 'resolving',
		rowKey: 'id',
		emptyMessage: __( 'No currencies found.', 'wp-ever-accounting' ),
		onChange: ( newQuery ) => {
			setSearchParams( new URLSearchParams( newQuery ) );
		},
		search: {
			placeholder: __( 'Search currencies', 'wp-ever-accounting' ),
		},
		onSearch: ( keyword ) => {
			console.log( keyword );
		},
		showSummary: true,
		renderSummary: ( column, data ) => {
			return (
				<>
					{ __( 'Total', 'wp-ever-accounting' ) }: { data.length }
				</>
			);
		},
		actions: [
			{
				label: __( 'Add New', 'wp-ever-accounting' ),
			},
			{
				label: __( 'Import', 'wp-ever-accounting' ),
			},
		],
	};

	return (
		<>
			<SectionHeader
				title={ __( 'Currencies', 'wp-ever-accounting' ) }
				style={ {
					marginBottom: '20px',
				} }
			/>

			<Table
				query={ query }
				columns={ columns }
				data={ currencies.records }
				pagination={ {
					total: currencies?.recordsCount,
					page: query?.page,
					perPage: query?.perPage,
				} }
				loading={ currencies.status === 'resolving' }
				rowKey="id"
				emptyMessage={ __( 'No currencies found.', 'wp-ever-accounting' ) }
				onChange={ ( newQuery ) => {
					setSearchParams( new URLSearchParams( newQuery ) );
				} }
				search={ {
					placeholder: __( 'Search currencies', 'wp-ever-accounting' ),
				} }
				onSearch={ ( keyword ) => {
					console.log( keyword );
				} }
				showSummary={ true }
				renderSummary={ ( column, data ) => {
					return (
						<>
							{ __( 'Total', 'wp-ever-accounting' ) }: { data.length }
						</>
					);
				} }
				actions={ [
					{
						label: __( 'Add New', 'wp-ever-accounting' ),
					},
					{
						label: __( 'Import', 'wp-ever-accounting' ),
					},
				] }
				renderExpanded={ ( record ) => {
					return (
						<div>
							{ record.name }
							Lorem ipsum dolor sit amet, consectetur adipisicing elit. At corporis
							eligendi, error exercitationem facilis hic itaque labore, magnam
							necessitatibus odit officia provident quae quam sed sint tempora velit.
							Accusamus commodi deleniti dicta dolorum illum impedit inventore ipsum
							laudantium magnam mollitia neque nobis, obcaecati, officiis quas
							repellendus rerum sunt temporibus. Quod.
						</div>
					);
				} }
			/>
		</>
	);
}

export default CurrenciesTable;
