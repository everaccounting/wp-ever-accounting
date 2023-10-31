/**
 * External dependencies
 */
import {
	SectionHeader,
	Input,
	Text,
	Badge,
	DropdownMenu,
} from '@eac/components';

/**
 * WordPress dependencies
 */
import { useState } from '@wordpress/element';
import { useSelect } from '@wordpress/data';
import { Button, Tip } from '@wordpress/components';
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
			<SectionHeader title={ __( 'Tools', 'wp-ever-accounting' ) }>
				<DropdownMenu label={ __( 'Filter', 'wp-ever-accounting' ) }>
					<DropdownMenu.Title> { __( 'Status', 'wp-ever-accounting' ) } </DropdownMenu.Title>
					<DropdownMenu.Item isCheckbox>
						{ __( 'Active', 'wp-ever-accounting' ) }
					</DropdownMenu.Item>
					<DropdownMenu.Item isCheckbox>
						{ __( 'Inactive', 'wp-ever-accounting' ) }
					</DropdownMenu.Item>
					<DropdownMenu.Item isCheckbox>
						{ __( 'Draft', 'wp-ever-accounting' ) }
					</DropdownMenu.Item>
				</DropdownMenu>
			</SectionHeader>
			<ProTable
				query={ query }
				onChange={ setSearchParams }
				headerTitle={
					<Text size={ 16 } weight={ 600 } as="h2" color="#23282d">
						{ __( 'Items', 'wp-ever-accounting' ) }
					</Text>
				}
				headerActions={
					<>
						<Button isPrimary={ true } icon="plus">
							Add Item
						</Button>
						<Button isSecondary={ true } icon="upload">
							Import
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
