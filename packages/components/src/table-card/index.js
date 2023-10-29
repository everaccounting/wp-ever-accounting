/**
 * WordPress dependencies
 */
import { Card, CardHeader, CardBody, CardFooter } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

/**
 * External dependencies
 */
import { noop } from 'lodash';
import classNames from 'classnames';
import PropTypes from 'prop-types';
/**
 * Internal dependencies
 */
import Table from '../table';
import { Text } from '../experimental';

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

function TableCard( props ) {
	const classes = classNames( 'eac-table-card', props.className );
	return (
		<Card
			className={ classes }
			style={ {
				borderRadius: '0',
			} }
		>
			<CardHeader>
				<Text size={ 16 } weight={ 600 } as="h2" color="#23282d">
					{ __( 'Dashboard', 'wp-ever-accounting' ) }
				</Text>
			</CardHeader>
			<CardBody style={ { padding: 0 } }>
				<Table
					columns={ [
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
							sortable: true,
						},
					] }
					dataSource={ data }
					onSort={ ( sort ) => console.log( sort ) }
				/>
			</CardBody>
			<CardFooter>Footer</CardFooter>
		</Card>
	);
}

export default TableCard;
