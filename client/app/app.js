import { useEntityRecord, useEntityRecords } from '@eac/store';
import { Table } from '@eac/components';
import { TextControl } from '@wordpress/components';
import { useDispatch } from '@wordpress/data';
import { useCallback } from '@wordpress/element';
import { __ } from '@wordpress/i18n';
import { store as noticeStore } from '@wordpress/notices';

const columns = [
	{
		title: 'Name',
		dataIndex: 'name',
		key: 'name',
		order: true,
	},
	{
		title: 'Age',
		dataIndex: 'age',
		key: 'age',
	},
	{
		title: 'Address',
		dataIndex: 'address',
		key: 'address',
	},
	{
		title: 'Operations',
		dataIndex: '',
		key: 'operations',
		render: () => <a href="#">Delete</a>,
	},
];

const data = [
	{ name: 'Jack', age: 28, address: 'some where', key: '1' },
	{ name: 'Rose', age: 36, address: 'some where', key: '2' },
];

// override rc-table's default tableClassName add widefat class.
const tableClassName = 'eac-table widefat';


export function App() {
	const { records, totalRecords, status, deleteRecord } =
		useEntityRecords('currency');

	return (
		<div>
			<Table
				columns={columns}
				dataSource={data}
			/>

			<p>Total records: {totalRecords}</p>
		</div>
	);
}

export default App;
