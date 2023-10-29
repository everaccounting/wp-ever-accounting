/**
 * External dependencies
 */
import { SectionHeader, Table, Input, Text } from '@eac/components';
import { useNavigate, useLocation, useSearchParams } from 'react-router-dom';

/**
 * WordPress dependencies
 */
import { createSlotFill, Card, CardHeader, CardBody, CardFooter } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import { useState } from '@wordpress/element';

const columns = [
	{
		type: 'expandable',
		width: 50,
	},
	{
		key: 'name',
		title: 'Name',
		property: 'name',
		sortable: true,
	},
	{
		key: 'age',
		title: 'Age',
		property: 'age',
		sortable: true,
	},
	{
		key: 'address',
		title: 'Address',
		property: 'address',
		sortable: true
	},
];
const data = [
	{
		date: '2016-05-03',
		name: 'Tom',
		state: 'California',
		city: 'Los Angeles',
		address: 'No. 189, Grove St, Los Angeles',
		zip: 'CA 90036',
	},
	{
		date: '2016-05-02',
		name: 'Tom',
		state: 'California',
		city: 'Los Angeles',
		address: 'No. 189, Grove St, Los Angeles',
		zip: 'CA 90036',
	},
	{
		date: '2016-05-04',
		name: 'Tom',
		state: 'California',
		city: 'Los Angeles',
		address: 'No. 189, Grove St, Los Angeles',
		zip: 'CA 90036',
	},
	{
		date: '2016-05-01',
		name: 'Tom',
		state: 'California',
		city: 'Los Angeles',
		address: 'No. 189, Grove St, Los Angeles',
		zip: 'CA 90036',
	},
	{
		date: '2016-05-08',
		name: 'Tom',
		state: 'California',
		city: 'Los Angeles',
		address: 'No. 189, Grove St, Los Angeles',
		zip: 'CA 90036',
	},
	{
		date: '2016-05-06',
		name: 'Tom',
		state: 'California',
		city: 'Los Angeles',
		address: 'No. 189, Grove St, Los Angeles',
		zip: 'CA 90036',
	},
	{
		date: '2016-05-07',
		name: 'Tom',
		state: 'California',
		city: 'Los Angeles',
		address: 'No. 189, Grove St, Los Angeles',
		zip: 'CA 90036',
	},
];
const { Fill } = createSlotFill( 'sub-header' );
function Dashboard( props ) {
	const [ searchParams, setSearchParams ] = useSearchParams();
	const query = Object.fromEntries( searchParams.entries() );
	const ToolbarItem = () => <Fill>My item</Fill>;
	return (
		<>
			<ToolbarItem />
			<SectionHeader title={ <>{ __( 'Dashboard', 'wp-ever-accounting' ) }</> } actions="test" menu="menu" />
			<Card>
				<CardHeader>
					<Text size={ 16 } weight={ 600 } as="h2" color="#23282d">
						{ __( 'Dashboard', 'wp-ever-accounting' ) }
					</Text>
				</CardHeader>
				<CardBody>
					<Table columns={ columns } dataSource={ data } query={ query } onSort={ ( sort ) => setSearchParams( { ...searchParams, ...sort, page: 1 } ) } />
				</CardBody>
				<CardFooter>
					Footer
				</CardFooter>
			</Card>
		</>
	);
}

export default Dashboard;
