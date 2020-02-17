import {Component} from 'react';
import {eAccountingApi, getApi} from './lib/api';
import {Table, Pagination, TextControl} from '@eaccounting/components';
import {__} from "@wordpress/i18n";
import Gridicon from 'gridicons';
import { Button } from '@wordpress/components';
const columns = [
	// {id: 'id', name: 'ID', sortable: true},
	{id: 'name', name: __('Name'), sortable: true},
	{id: 'number', name: __('Number'), sortable: true},
	{id: 'balance', name: __('Balance'), sortable: false},
	{id: 'status', name: __('Status'), sortable: true},
	{id: 'action', name: __('Actions'), sortable: false},
];

// const rows = [{id: 0, title: 'row1', count: 20}, {id: 1, title: 'row1', count: 40}, {id: 2, title: 'row1', count: 60}];

export default class App extends Component {
	constructor(props) {
		super(props);
		this.state = {
			rows: [],
			status: '',
			table: {
				orderby: 'id',
				order: 'asc',
				page: 1,
				total: 0,
				perpage: 20,
			}
		};
	}

	componentDidMount() {
		this.getAccounts({}, (json) => {
			this.setState({
				status: 'ready',
				rows: json.data,
				table: {
					...this.state.table,
					total: json.total
				}
			})
		})
	}

	getAccounts = (params, callback) => {
		getApi(eAccountingApi.accounts.list(params)).then(json => callback(json));
	};

	onSort = (orderby, order) => {
		this.setState({
			status: '',
		});

		this.getAccounts({...this.state.table, orderby, order}, (json) => {
			this.setState({
				status: 'ready',
				rows: json.data,
				table: {
					...this.state.table,
					total: json.total,
					orderby, order
				}
			})
		})
	};

	onPageChange = (page) => {
		this.setState({
			status: '',
		});
		this.getAccounts({...this.state.table, page}, (json) => {
			this.setState({
				status: 'ready',
				rows: json.data,
				table: {
					...this.state.table,
					page:page
				}
			})
		})
	};

	render() {
		const {rows, status} = this.state;
		const {orderby, order, page, total, perpage} = this.state.table;
		// rows = rows.map((row) => {
		//
		// });
		return (
			<div>

				<TextControl label='Text'/>
				<Button><Gridicon icon="ellipsis" /></Button>
				<div className="tablenav">
					<input type="text" placeholder='Search'/>
					<button className='button button-secondary'>Filter</button>
					<Pagination total={total} per_page={perpage} page={page} onChangePage={this.onPageChange}
								status={status}/>
				</div>
				<Table columns={columns} rows={rows} onSort={this.onSort} orderby={orderby} order={order}
					   status={status}/>
				<div className="tablenav">
					<Pagination total={total} per_page={perpage} page={page} onChangePage={this.onPageChange}
								status={status}/>
				</div>
			</div>
		)
	}
}
