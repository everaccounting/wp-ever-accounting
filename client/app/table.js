export const columns = [
	{
		type: 'selection'
	},
	{
		label: "Date",
		prop: "date",
		width: 150,
		fixed: 'left',
		align: 'center',
		key: 'date',
		sortable: true,
		render: (row) => {
			return (<a href="#">{row.date}</a>)
		}
	},
	{
		label: "Name",
		prop: "name",
		key: 'name',
		width: 120,
		sortable: true
	},
	{
		label: "State",
		prop: "state",
		key: 'state',
		width: 120,
		sortable: true
	},
	{
		label: "City",
		prop: "city",
		key: 'city',
		width: 120,
		sortable: true
	},
	{
		label: "Address",
		prop: "address",
		width: 300,
		sortable: true
	},
	{
		label: "Zip",
		prop: "zip",
		width: 120,
		sortable: true
	},
	{
		label: "Operations",
		width: 120,
		fixed: 'right',
		render: (row, column, index) => {
			return <span><span type="text" size="small" onClick={() => console.log(index)}>Remove</span></span>
		}
	}
];
export const data = [
	{
		date: '2016-05-03',
		name: 'Tom',
		state: 'California',
		city: 'Los Angeles',
		address: 'No. 189, Grove St, Los Angeles',
		zip: 'CA 90036'
	}, {
		date: '2016-05-02',
		name: 'Tom',
		state: 'California',
		city: 'Los Angeles',
		address: 'No. 189, Grove St, Los Angeles',
		zip: 'CA 90036'
	}, {
		date: '2016-05-04',
		name: 'Tom',
		state: 'California',
		city: 'Los Angeles',
		address: 'No. 189, Grove St, Los Angeles',
		zip: 'CA 90036'
	}, {
		date: '2016-05-01',
		name: 'Tom',
		state: 'California',
		city: 'Los Angeles',
		address: 'No. 189, Grove St, Los Angeles',
		zip: 'CA 90036'
	}, {
		date: '2016-05-08',
		name: 'Tom',
		state: 'California',
		city: 'Los Angeles',
		address: 'No. 189, Grove St, Los Angeles',
		zip: 'CA 90036'
	}, {
		date: '2016-05-06',
		name: 'Tom',
		state: 'California',
		city: 'Los Angeles',
		address: 'No. 189, Grove St, Los Angeles',
		zip: 'CA 90036'
	}, {
		date: '2016-05-07',
		name: 'Tom',
		state: 'California',
		city: 'Los Angeles',
		address: 'No. 189, Grove St, Los Angeles',
		zip: 'CA 90036'
	},
	{
		date: '2016-05-03',
		name: 'Tom',
		state: 'California',
		city: 'Los Angeles',
		address: 'No. 189, Grove St, Los Angeles',
		zip: 'CA 90036'
	}, {
		date: '2016-05-02',
		name: 'Tom',
		state: 'California',
		city: 'Los Angeles',
		address: 'No. 189, Grove St, Los Angeles',
		zip: 'CA 90036'
	}, {
		date: '2016-05-04',
		name: 'Tom',
		state: 'California',
		city: 'Los Angeles',
		address: 'No. 189, Grove St, Los Angeles',
		zip: 'CA 90036'
	}, {
		date: '2016-05-01',
		name: 'Tom',
		state: 'California',
		city: 'Los Angeles',
		address: 'No. 189, Grove St, Los Angeles',
		zip: 'CA 90036'
	}, {
		date: '2016-05-08',
		name: 'Tom',
		state: 'California',
		city: 'Los Angeles',
		address: 'No. 189, Grove St, Los Angeles',
		zip: 'CA 90036'
	}, {
		date: '2016-05-06',
		name: 'Tom',
		state: 'California',
		city: 'Los Angeles',
		address: 'No. 189, Grove St, Los Angeles',
		zip: 'CA 90036'
	}, {
		date: '2016-05-07',
		name: 'Tom',
		state: 'California',
		city: 'Los Angeles',
		address: 'No. 189, Grove St, Los Angeles',
		zip: 'CA 90036'
	}
];
