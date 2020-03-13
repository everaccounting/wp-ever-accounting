import {Component, Fragment} from "@wordpress/element";
import {QUERY_STATE_STORE_KEY, TABLE_STORE_KEY} from "@eaccounting/data";
import {withDispatch, withSelect, select} from '@wordpress/data';
import {compose} from '@wordpress/compose';
import {Button, TableNav, SearchBox, Table} from '@eaccounting/components';
import {getHeaders, getBulk} from './constants';
import {__} from "@wordpress/i18n";

const endpoint = 'ea/v1/categories';
import Row from "./row";

class Categories extends Component {

	constructor(props) {
		super(props);
	}

	componentDidMount() {
		this.props.loadItems(endpoint, {});
	}

	onRenderRow = (item, pos, status, search) => {
		const {selected} = this.props.table;
		return (
			<Row
				item={item}
				key={pos}
				disabled={status.isLoading}
				search={search}
				isSelected={selected.includes(item.id)}
				{...this.props}
			/>
		);
	};

	render() {
		const {status, total, table, items, match} = this.props;
		return (
			<Fragment>


				<div className="ea-table-display">
					<Button className="page-title-action" onClick={this.onAdd}>
						{__('Add Category')}
					</Button>
					<SearchBox status={status} table={table} onSearch={(search)=> this.props.loadItems(endpoint, {search})}/>
				</div>


				<TableNav
					total={total}
					selected={table.selected}
					table={table}
					onChangePage={(page)=> this.props.loadItems(endpoint, {page})}
					onAction={this.props.onAction}
					status={status}
					bulk={getBulk()}
				/>

				<Table
					headers={getHeaders()}
					rows={items}
					total={total}
					row={this.onRenderRow}
					table={table}
					status={status}
					onSetAllSelected={this.props.onSetAllSelected}
					onSetOrderBy={(orderby, order)=> this.props.loadItems(endpoint, {orderby, order})}
				/>

				{/*<TableNav*/}
				{/*	page={page}*/}
				{/*	per_page={per_page}*/}
				{/*	total={total}*/}
				{/*	selected={selected}*/}
				{/*	onChangePage={(page)=>  {this.props.setQuery(endpoint, 'page', page)}}*/}
				{/*	status={status}*/}
				{/*/>*/}

			</Fragment>
		)
	}
}

export default compose(withSelect(select => {
	const store = select(TABLE_STORE_KEY);
	// const {page = 1, orderby = 'name', order = 'desc'} = query;
	// const isLoading = store.hasFinishedResolution('getItems', [endpoint, query]) === false;
	// const status = isLoading ? "IN_PROGRESS" :store.getStatus(endpoint);
	return {
		items: store.getItems(),
		total: store.getTotal(),
		table: store.getTable(),
		status: store.getStatus(),
		per_page: 20,
		page: 1,
		orderby: 'id',
		order: 'desc'
	}

}), withDispatch(dispatch => {
	return {
		loadItems: dispatch(TABLE_STORE_KEY).loadItems,
		onSearch: () => {
		},
		onChangePage: () => {
		},
		onSetOrderBy: () => {
		},
		onSetAllSelected: () => {
		},
		onAdd: () => {
		},
		onAction: () => {
		},
	}
}))(Categories)
