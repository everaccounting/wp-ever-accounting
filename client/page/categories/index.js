import {Component, Fragment} from "@wordpress/element";
import {STORE_COLLECTION_KEY, QUERY_STATE_STORE_KEY} from "@eaccounting/data";
import {withDispatch, withSelect, select} from '@wordpress/data';
import {compose} from '@wordpress/compose';
import {withTable, Spinner, SearchBox, Button, TableNav, ListTable} from "@eaccounting/components";
import {getHeaders, getBulk} from './constants';
import {__} from "@wordpress/i18n";
const endpoint = 'categories';
import Row from "./row";

class Categories extends Component {

	constructor(props) {
		super(props);
	}

	onRenderRow = (item, pos, status, search) => {
		const { selected } = this.props;
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
		const {items, total, page, orderby, order, status, selected, per_page} = this.props;
		console.log(items);
		console.log(selected);
		return (
			<Fragment>


				{/*<div className="ea-table-display">*/}
				{/*	<Button className="page-title-action" onClick={this.onAdd}>*/}
				{/*		{__('Add Category')}*/}
				{/*	</Button>*/}
				{/*	<SearchBox status={status} onSearch={(search)=> {this.props.setQuery(endpoint, 'search', search)}}/>*/}
				{/*</div>*/}


				{/*<TableNav*/}
				{/*	page={page}*/}
				{/*	per_page={per_page}*/}
				{/*	total={total}*/}
				{/*	selected={selected}*/}
				{/*	onChangePage={(page)=>  {this.props.setQuery(endpoint, 'page', page)}}*/}
				{/*	status={status}*/}
				{/*	bulk={getBulk()}*/}
				{/*/>*/}

				{/*<ListTable*/}
				{/*	headers={getHeaders()}*/}
				{/*	rows={items}*/}
				{/*	selected={selected}*/}
				{/*	orderby={'id'}*/}
				{/*	order={'desc'}*/}
				{/*	total={total}*/}
				{/*	status={status}*/}
				{/*	onSetOrderBy={(orderby, order)=> {}}*/}
				{/*	row={this.onRenderRow}*/}
				{/*/>*/}

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
	const query = select(QUERY_STATE_STORE_KEY).getValueForQueryContext(endpoint);
	const store = select(STORE_COLLECTION_KEY);
	const {page = 1, orderby = 'name', order = 'desc'} = query;
	const isLoading = store.hasFinishedResolution('getCollection', [endpoint, query]) === false;
	const status = isLoading ? "IN_PROGRESS" :store.getStatus(endpoint);
	return {
		items: store.getCollection(endpoint, query),
		total: store.getTotal(endpoint, query),
		selected:store.getSelected(endpoint, query),
		query: query,
		status,
		per_page: 20,
		page,
		orderby,
		order
	}

}), withDispatch(dispatch => {
	return {
		setQuery: dispatch(QUERY_STATE_STORE_KEY).setQueryValue,
	}
}))(Categories)
